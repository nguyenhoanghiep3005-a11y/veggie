<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WarehouseDamage;
use App\Models\WarehouseStock;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    protected $cloudinary;

    // Gắn service upload ảnh/video minh chứng hàng lỗi.
    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    // Hiển thị danh sách tồn kho theo trạng thái lọc.
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $stocks = $this->stockQuery($status)
            ->orderByRaw('expired_at IS NULL')
            ->orderBy('expired_at')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.warehouses', compact('stocks', 'status'));
    }

    // Hiển thị danh sách hàng hư/lỗi đã ghi nhận.
    public function damages()
    {
        $damages = WarehouseDamage::with(['product', 'mediaFiles'])
            ->orderBy('occurred_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.pages.warehouse-damages', compact('damages'));
    }

    // Điều chỉnh kho: set giá khuyến mãi hoặc ghi nhận hàng hư/lỗi.
    public function adjust(Request $request, WarehouseStock $stock)
    {
        $data = $request->validate([
            'action' => 'required|in:promotion,damage',
            'sale_price' => 'nullable|numeric|min:0',
            'damage_quantity' => 'nullable|integer|min:1',
            'damage_reason' => 'nullable|string|max:3000',
            'evidence' => 'nullable|array',
            'evidence.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
        ], [
            'evidence.*.mimes' => 'Minh chứng phải là ảnh hoặc video hợp lệ.',
            'evidence.*.max' => 'Mỗi ảnh hoặc video không được vượt quá 50MB.',
        ]);

        if ($data['action'] == 'promotion') {
            return $this->adjustPromotion($stock, $data);
        }

        $evidenceFiles = [];
        $files = $request->file('evidence', []);
        foreach ($files as $file) {
            if ($file) {
                $evidenceFiles[] = $file;
            }
        }

        return $this->recordDamage($stock, $data, $evidenceFiles);
    }

    // Tạo query lấy tồn kho theo bộ lọc trên trang kho.
    private function stockQuery($status)
    {
        $query = WarehouseStock::with(['product.category', 'receipt', 'supplier'])
            ->whereHas('product')
            ->where('quantity_remaining', '>', 0);

        if ($status == 'near') {
            $query->whereDate('expired_at', '>=', today())
                ->whereDate('expired_at', '<=', today()->addDays(Product::NEAR_EXPIRY_DAYS));
        }

        if ($status == 'expired') {
            $query->whereDate('expired_at', '<', today());
        }

        if ($status == 'fresh' || $status == 'normal') {
            $query->where(function ($builder) {
                $builder->whereNull('expired_at')
                    ->orWhereDate('expired_at', '>', today()->addDays(Product::NEAR_EXPIRY_DAYS));
            });
        }

        return $query;
    }

    // Cập nhật giá khuyến mãi cho một lô hàng trong kho.
    private function adjustPromotion($stock, $data)
    {
        $stock->load('product');
        $product = $stock->product;

        if (! $product) {
            return back()->withErrors(['product' => 'Sản phẩm của kho hàng không còn tồn tại.']);
        }

        if ($stock->isExpired()) {
            return back()->withErrors(['sale_price' => 'Hàng hết hạn không được điều chỉnh giá bán.']);
        }

        $salePrice = null;
        if (! empty($data['sale_price'])) {
            $salePrice = (float) $data['sale_price'];
        }

        if ($salePrice != null && $salePrice >= (float) $product->price) {
            return back()
                ->withErrors(['sale_price' => 'Giá điều chỉnh phải nhỏ hơn giá niêm yết.'])
                ->withInput();
        }

        $stock->update(['sale_price' => $salePrice]);

        return back()->with('success', 'Đã cập nhật giá điều chỉnh cho hàng trong kho.');
    }

    // Ghi nhận hàng hư/lỗi, trừ tồn kho và lưu minh chứng.
    private function recordDamage($stock, $data, $evidenceFiles)
    {
        $quantity = 0;
        $reason = '';

        if (isset($data['damage_quantity'])) {
            $quantity = (int) $data['damage_quantity'];
        }

        if (isset($data['damage_reason'])) {
            $reason = trim($data['damage_reason']);
        }

        if ($quantity <= 0) {
            return back()->withErrors(['damage_quantity' => 'Vui lòng nhập số lượng hư/lỗi.'])->withInput();
        }

        if ($reason == '') {
            return back()->withErrors(['damage_reason' => 'Vui lòng nhập lý do hư/lỗi.'])->withInput();
        }

        if (count($evidenceFiles) == 0) {
            return back()->withErrors([
                'evidence' => 'Hàng hư/lỗi phải có ít nhất một ảnh hoặc video minh chứng.',
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $stock = WarehouseStock::with('product')->lockForUpdate()->findOrFail($stock->id);

            if ($quantity > (int) $stock->quantity_remaining) {
                throw new Exception('Số lượng hư/lỗi không được lớn hơn số lượng còn trong kho.');
            }

            $product = Product::lockForUpdate()->findOrFail($stock->product_id);
            if ($product->stock < $quantity) {
                throw new Exception('Tồn sản phẩm không đủ để ghi nhận hư/lỗi.');
            }

            $stock->quantity_remaining = $stock->quantity_remaining - $quantity;
            $stock->save();

            $product->stock = $product->stock - $quantity;
            $product->refreshStatus();
            $product->save();

            $warehouseDamage = WarehouseDamage::create([
                'warehouse_stock_id' => $stock->id,
                'product_id' => $product->id,
                'product_name' => $product->display_name,
                'quantity' => $quantity,
                'reason' => $reason,
                'occurred_at' => now(),
            ]);

            $media = $this->cloudinary->uploadMany(
                $evidenceFiles,
                config('cloudinary.folders.warehouse_damages')
            );

            foreach ($media as $item) {
                $this->saveDamageMedia($warehouseDamage, $item);
            }

            DB::commit();

            return redirect()
                ->route('admin.warehouses.damages')
                ->with('success', 'Đã ghi nhận hàng hư hỏng/lỗi trong kho.');
        } catch (Exception $exception) {
            DB::rollBack();

            return back()->withInput()->withErrors(['damage' => $exception->getMessage()]);
        }
    }

    // Lưu một file minh chứng của phiếu hàng hư/lỗi.
    private function saveDamageMedia($warehouseDamage, $item)
    {
        $disk = 'public';
        $path = '';
        $originalName = null;
        $mimeType = null;
        $mediaType = 'image';
        $size = 0;

        if (isset($item['disk'])) {
            $disk = $item['disk'];
        }
        if (isset($item['path'])) {
            $path = $item['path'];
        }
        if (isset($item['original_name'])) {
            $originalName = $item['original_name'];
        }
        if (isset($item['mime_type'])) {
            $mimeType = $item['mime_type'];
        }
        if (isset($item['media_type'])) {
            $mediaType = $item['media_type'];
        }
        if (isset($item['size'])) {
            $size = $item['size'];
        }

        $warehouseDamage->mediaFiles()->create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'size' => $size,
        ]);
    }
}
