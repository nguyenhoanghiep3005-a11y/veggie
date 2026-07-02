<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $this->refreshInventoryConditions();

        $products = Product::with('category', 'inventories')->orderBy('name')->get();
        $inventories = Inventory::with('product.category')
            ->orderBy('expired_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.pages.inventories', compact('products', 'inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_imported' => 'required|integer|min:1',
            'imported_at' => 'required|date|before_or_equal:today',
            'expired_at' => 'required|date|after_or_equal:imported_at',
            'adjusted_price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantityImported = (int) $request->quantity_imported;
        $adjustedPrice = $this->getAdjustedPrice($request->adjusted_price);

        $priceError = $this->validateAdjustedPrice($adjustedPrice, $product);

        if ($priceError) {
            return redirect()->route('admin.inventories.index')->with('error', $priceError);
        }

        $inventory = $this->findMatchingInventory(
            $product->id,
            $request->imported_at,
            $request->expired_at,
            $adjustedPrice
        );

        if ($inventory) {
            $inventory->quantity_imported += $quantityImported;
            $inventory->quantity_remaining += $quantityImported;
            $inventory->condition = Inventory::checkCondition(
                'fresh',
                $inventory->quantity_remaining,
                $inventory->expired_at->toDateString()
            );

            if (!$inventory->note && $request->note) {
                $inventory->note = $request->note;
            }

            $inventory->save();
            $message = 'Lô hàng đã tồn tại, hệ thống đã cộng dồn số lượng vào ' . $inventory->lotCode() . '.';
        } else {
            $inventory = Inventory::create([
                'product_id' => $product->id,
                'quantity_imported' => $quantityImported,
                'quantity_remaining' => $quantityImported,
                'quantity_damaged' => 0,
                'imported_at' => $request->imported_at,
                'expired_at' => $request->expired_at,
                'condition' => Inventory::checkCondition('fresh', $quantityImported, $request->expired_at),
                'adjusted_price' => $adjustedPrice,
                'note' => $request->note,
            ]);
            $message = 'Thêm lô hàng vào kho thành công!';
        }

        $this->updateProductStatus($inventory->product);

        return redirect()->route('admin.inventories.index')
            ->with('success', $message);
    }

    public function update(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'damaged_item_numbers' => 'nullable|array',
            'damaged_item_numbers.*' => 'integer|min:1',
            'adjusted_price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $inventory = Inventory::with('product')->findOrFail($request->inventory_id);
        $damagedItemNumbers = $this->getDamagedItemNumbers($request->damaged_item_numbers);
        $soldItemNumbers = $inventory->soldItemNumbers();
        $maxUnsoldQuantity = $inventory->maxUnsoldQuantity();

        foreach ($damagedItemNumbers as $number) {
            if ($number > $inventory->quantity_imported) {
                return redirect()->route('admin.inventories.index')
                    ->with('error', 'Mã hàng hư không hợp lệ với số lượng nhập của lô.');
            }

            if (in_array($number, $soldItemNumbers, true)) {
                return redirect()->route('admin.inventories.index')
                    ->with('error', 'Không thể chọn mã hàng đã bán làm hàng hư.');
            }
        }

        if (count($damagedItemNumbers) > $maxUnsoldQuantity) {
            return redirect()->route('admin.inventories.index')
                ->with('error', 'Số lượng mã hư không được lớn hơn số lượng chưa bán của lô.');
        }

        $adjustedPrice = $this->getAdjustedPrice($request->adjusted_price);

        $priceError = $this->validateAdjustedPrice($adjustedPrice, $inventory->product);

        if ($priceError) {
            return redirect()->route('admin.inventories.index')->with('error', $priceError);
        }

        $quantityRemaining = $maxUnsoldQuantity - count($damagedItemNumbers);

        $inventory->quantity_remaining = $quantityRemaining;
        $inventory->setDamagedItemNumbers($damagedItemNumbers);
        $inventory->condition = Inventory::checkCondition('fresh', $quantityRemaining, $inventory->expired_at->toDateString());
        $inventory->adjusted_price = $adjustedPrice;
        $inventory->note = $request->note;
        $inventory->save();

        $this->updateProductStatus($inventory->product);

        return redirect()->route('admin.inventories.index')
            ->with('success', 'Cập nhật lô hàng thành công!');
    }

    private function updateProductStatus($product)
    {
        $product->status = $product->availableInventories()->sum('quantity_remaining') > 0
            ? 'int_stock'
            : 'out_of_stock';

        $product->save();
    }

    private function refreshInventoryConditions()
    {
        $inventories = Inventory::with('product')->get();

        foreach ($inventories as $inventory) {
            $oldCondition = $inventory->condition;
            $inventory->refreshCondition();

            if ($oldCondition !== $inventory->condition) {
                $inventory->save();
            }

            if ($inventory->product) {
                $this->updateProductStatus($inventory->product);
            }
        }
    }

    private function getAdjustedPrice($adjustedPrice)
    {
        if ($adjustedPrice === null || $adjustedPrice === '' || $adjustedPrice <= 0) {
            return null;
        }

        return $adjustedPrice;
    }

    private function validateAdjustedPrice($adjustedPrice, Product $product)
    {
        if ($adjustedPrice !== null && $adjustedPrice >= $product->price) {
            return 'Giá điều chỉnh trong kho chỉ dùng để giảm giá. Nếu muốn tăng giá bán, hãy cập nhật giá niêm yết của sản phẩm.';
        }

        return null;
    }

    private function findMatchingInventory($productId, $importedAt, $expiredAt, $adjustedPrice)
    {
        $query = Inventory::where('product_id', $productId)
            ->whereDate('imported_at', $importedAt)
            ->whereDate('expired_at', $expiredAt);

        if ($adjustedPrice === null) {
            $query->whereNull('adjusted_price');
        } else {
            $query->where('adjusted_price', $adjustedPrice);
        }

        return $query->orderBy('id')->first();
    }


    private function getDamagedItemNumbers($numbers)
    {
        if (!$numbers) {
            return [];
        }

        $damagedItemNumbers = [];

        foreach ($numbers as $number) {
            $number = (int) $number;

            if ($number > 0 && !in_array($number, $damagedItemNumbers, true)) {
                $damagedItemNumbers[] = $number;
            }
        }

        sort($damagedItemNumbers);

        return $damagedItemNumbers;
    }
}
