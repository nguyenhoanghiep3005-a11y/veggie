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

        $product = Product::find($request->product_id);
        $adjustedPrice = $this->getAdjustedPrice($request->adjusted_price);

        if ($adjustedPrice !== null && $adjustedPrice >= $product->price) {
            return redirect()->route('admin.inventories.index')
                ->with('error', 'Giá điều chỉnh phải nhỏ hơn giá niêm yết.');
        }

        $quantityDamaged = 0;
        $quantityRemaining = (int) $request->quantity_imported;
        $condition = Inventory::checkCondition('fresh', $quantityRemaining, $request->expired_at);

        $inventory = Inventory::create([
            'product_id' => $request->product_id,
            'quantity_imported' => $request->quantity_imported,
            'quantity_remaining' => $quantityRemaining,
            'quantity_damaged' => $quantityDamaged,
            'imported_at' => $request->imported_at,
            'expired_at' => $request->expired_at,
            'condition' => $condition,
            'adjusted_price' => $adjustedPrice,
            'note' => $request->note,
        ]);

        $this->updateProductStatus($inventory->product);

        return redirect()->route('admin.inventories.index')
            ->with('success', 'Thêm lô hàng vào kho thành công!');
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

        $inventory = Inventory::with('product')->find($request->inventory_id);
        $soldQuantity = $inventory->soldQuantity();
        $maxUnsoldQuantity = $inventory->quantity_imported - $soldQuantity;
        $damagedItemNumbers = $this->getDamagedItemNumbers($request->damaged_item_numbers);
        $soldItemNumbers = $inventory->soldItemNumbers();

        foreach ($damagedItemNumbers as $number) {
            if ($number > $inventory->quantity_imported) {
                return redirect()->route('admin.inventories.index')
                    ->with('error', 'Mã hàng hư không hợp lệ với số lượng nhập của lô.');
            }

            if (in_array($number, $soldItemNumbers)) {
                return redirect()->route('admin.inventories.index')
                    ->with('error', 'Không thể chọn mã hàng đã bán làm hàng hư.');
            }
        }

        if (count($damagedItemNumbers) > $maxUnsoldQuantity) {
            return redirect()->route('admin.inventories.index')
                ->with('error', 'Số lượng mã hư không được lớn hơn số lượng chưa bán của lô.');
        }

        $quantityDamaged = count($damagedItemNumbers);
        $quantityRemaining = $maxUnsoldQuantity - $quantityDamaged;

        $adjustedPrice = $this->getAdjustedPrice($request->adjusted_price);

        if ($adjustedPrice !== null && $adjustedPrice >= $inventory->product->price) {
            return redirect()->route('admin.inventories.index')
                ->with('error', 'Giá điều chỉnh phải nhỏ hơn giá niêm yết.');
        }

        if ($quantityRemaining == 0 && $quantityDamaged > 0) {
            $condition = 'damaged';
        } else {
            $condition = Inventory::checkCondition('fresh', $quantityRemaining, $inventory->expired_at);
        }

        $inventory->quantity_remaining = $quantityRemaining;
        $inventory->setDamagedItemNumbers($damagedItemNumbers);
        $inventory->condition = $condition;
        $inventory->adjusted_price = $adjustedPrice;
        $inventory->note = $request->note;
        $inventory->save();

        $this->updateProductStatus($inventory->product);

        return redirect()->route('admin.inventories.index')
            ->with('success', 'Cập nhật lô hàng thành công!');
    }

    private function updateProductStatus($product)
    {
        $availableQuantity = $product->availableInventories()->sum('quantity_remaining');

        if ($availableQuantity > 0) {
            $product->status = 'int_stock';
        } else {
            $product->status = 'out_of_stock';
        }

        $product->save();
    }

    private function refreshInventoryConditions()
    {
        $inventories = Inventory::with('product')->get();

        foreach ($inventories as $inventory) {
            $oldCondition = $inventory->condition;
            $inventory->refreshCondition();

            if ($oldCondition != $inventory->condition) {
                $inventory->save();

                if ($inventory->product) {
                    $this->updateProductStatus($inventory->product);
                }
            }
        }

        $products = Product::all();
        foreach ($products as $product) {
            $this->updateProductStatus($product);
        }
    }

    private function getAdjustedPrice($adjustedPrice)
    {
        if ($adjustedPrice === null || $adjustedPrice === '') {
            return null;
        }

        if ($adjustedPrice <= 0) {
            return null;
        }

        return $adjustedPrice;
    }

    private function getDamagedItemNumbers($numbers)
    {
        if (!$numbers) {
            return [];
        }

        $damagedItemNumbers = [];

        foreach ($numbers as $number) {
            $number = (int) $number;

            if ($number > 0 && !in_array($number, $damagedItemNumbers)) {
                $damagedItemNumbers[] = $number;
            }
        }

        sort($damagedItemNumbers);

        return $damagedItemNumbers;
    }
}
