<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const NEAR_EXPIRY_DAYS = 60;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'description',
        'price',
        'stock',
        'status',
        'unit',
        'average_rating',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'average_rating' => 'decimal:2',
    ];

    // Liên kết sản phẩm với danh mục.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Lấy toàn bộ ảnh của sản phẩm.
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('id', 'ASC');
    }

    // Lấy ảnh đầu tiên của sản phẩm.
    public function firstImage()
    {
        return $this->hasOne(ProductImage::class)->orderBy('id', 'ASC');
    }

    // Lấy danh sách đánh giá sản phẩm.
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Lấy các dòng đơn hàng có sản phẩm này.
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Tính tổng số lượng đã bán, không tính đơn đã hủy.
    public function soldQuantity()
    {
        $soldQuantity = 0;

        if ($this->relationLoaded('orderItems')) {
            $orderItems = $this->orderItems;
        } else {
            $orderItems = $this->orderItems()->with('order')->get();
        }

        foreach ($orderItems as $item) {
            if (! $item->order) {
                continue;
            }

            if ($item->order->status == 'canceled' || $item->order->status == 'cancelled') {
                continue;
            }

            $soldQuantity += (int) $item->quantity;
        }

        return $soldQuantity;
    }

    // Lấy chi tiết đơn mua hàng nhập kho.
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Lấy chi tiết phiếu nhập kho.
    public function importReceiptItems()
    {
        return $this->hasMany(ImportReceiptItem::class);
    }

    // Lấy các lô tồn kho của sản phẩm.
    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }

    // Lấy các dòng phiếu hủy hoặc hư hỏng của sản phẩm.
    public function damageSlipItems()
    {
        return $this->hasMany(DamageSlipItem::class);
    }

    // Ép tồn kho về số nguyên khi gọi $product->stock.
    public function getStockAttribute($value)
    {
        return (int) $value;
    }

    // Lấy giá đang bán, ưu tiên giá khuyến mãi của lô còn bán.
    public function getCurrentPriceAttribute()
    {
        $firstLot = $this->firstSellableLot();

        if ($firstLot) {
            return $this->priceForLot($firstLot);
        }

        return $this->baseSellingPrice();
    }

    // Lấy giá gốc của sản phẩm.
    public function baseSellingPrice()
    {
        return (float) $this->price;
    }

    // Kiểm tra sản phẩm có đang khuyến mãi không.
    public function getIsOnSaleAttribute()
    {
        return (float) $this->current_price < (float) $this->price;
    }

    // Lấy tên gốc của sản phẩm, bỏ phần đơn vị như 500g hoặc 1kg.
    public function getBaseNameAttribute()
    {
        $name = trim((string) $this->name);
        $unit = trim((string) $this->variant_label);

        if ($unit !== '' && $unit !== 'Mặc định') {
            $name = preg_replace('/\s*'.preg_quote($unit, '/').'$/iu', '', $name);
        }

        $name = preg_replace('/\s+\d+(?:[,.]\d+)?\s*(g|gram|kg)$/iu', '', $name);
        $name = trim($name);

        if ($name !== '') {
            return $name;
        }

        return trim((string) $this->name);
    }

    // Lấy nhãn đơn vị hiển thị cho biến thể sản phẩm.
    public function getVariantLabelAttribute()
    {
        $unit = trim((string) $this->unit);
        if ($unit !== '') {
            return str_replace(' ', '', $unit);
        }

        if (preg_match('/(\d+(?:[,.]\d+)?\s*(g|gram|kg))$/iu', (string) $this->name, $matches)) {
            $label = str_replace(' ', '', $matches[1]);

            return preg_replace('/gram$/i', 'g', $label);
        }

        return 'Mặc định';
    }

    // Ghép tên gốc và đơn vị để hiển thị ngoài client.
    public function getDisplayNameAttribute()
    {
        $baseName = $this->base_name;
        $unit = $this->variant_label;

        if ($unit === '' || $unit === 'Mặc định') {
            return $baseName;
        }

        return trim($baseName.' '.$unit);
    }

    // Lấy ảnh hiển thị chính, nếu thiếu thì lấy ảnh biến thể cùng loại.
    public function getImageUrlAttribute()
    {
        if ($this->firstImage && $this->firstImage->image) {
            return asset('storage/'.$this->firstImage->image);
        }

        $fallbackImage = $this->variantFallbackImage();
        if ($fallbackImage) {
            return asset('storage/'.$fallbackImage->image);
        }

        return asset('storage/uploads/products/default.png');
    }

    // Tính tổng tiền theo số lượng, ưu tiên lô có giá khuyến mãi.
    public function calculatePriceByQuantity($quantity)
    {
        $remaining = max(0, (int) $quantity);
        $total = 0;

        if ($remaining <= 0) {
            return 0;
        }

        foreach ($this->sellableLots()->get() as $lot) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($remaining, (int) $lot->quantity_remaining);
            $total += $take * $this->priceForLot($lot);
            $remaining -= $take;
        }

        if ($remaining > 0) {
            $total += $remaining * $this->baseSellingPrice();
        }

        return $total;
    }

    // Tính đơn giá trung bình cho số lượng đang mua.
    public function unitPriceForQuantity($quantity)
    {
        $quantity = max(1, $quantity);

        return $this->calculatePriceByQuantity($quantity) / $quantity;
    }

    // Lấy giá bán của một lô hàng.
    public function priceForLot($lot)
    {
        $salePrice = 0;

        if (isset($lot->sale_price)) {
            $salePrice = (float) $lot->sale_price;
        }

        if ($salePrice > 0 && $salePrice < (float) $this->price) {
            return $salePrice;
        }

        return $this->baseSellingPrice();
    }

    // Lấy lô hàng đầu tiên còn bán được.
    public function firstSellableLot()
    {
        return $this->sellableLots()->first();
    }

    // Lấy các lô còn hàng và chưa hết hạn.
    public function sellableLots()
    {
        return $this->warehouseStocks()
            ->where('quantity_remaining', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhereDate('expired_at', '>=', today());
            })
            ->orderBy('expired_at')
            ->orderBy('id');
    }

    // Tính tổng tồn kho còn bán được.
    public function sellableStock()
    {
        if (! $this->warehouseStocks()->exists()) {
            return $this->stock;
        }

        return (int) $this->warehouseStocks()
            ->where('quantity_remaining', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhereDate('expired_at', '>=', today());
            })
            ->sum('quantity_remaining');
    }

    // Tính số lượng hàng sắp hết hạn.
    public function nearExpiryQuantity()
    {
        return (int) $this->warehouseStocks()
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expired_at', '>=', today())
            ->whereDate('expired_at', '<=', today()->addDays(self::NEAR_EXPIRY_DAYS))
            ->sum('quantity_remaining');
    }

    // Tính số lượng hàng đã hết hạn.
    public function expiredQuantity()
    {
        return (int) $this->warehouseStocks()
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expired_at', '<', today())
            ->sum('quantity_remaining');
    }

    // Tính số lượng hàng bị hủy hoặc hư hỏng.
    public function damagedQuantity()
    {
        return (int) $this->damageSlipItems()->sum('quantity');
    }

    // Lấy ngày hết hạn gần nhất của sản phẩm.
    public function nearestExpiryDate()
    {
        return $this->warehouseStocks()
            ->where('quantity_remaining', '>', 0)
            ->whereNotNull('expired_at')
            ->orderBy('expired_at')
            ->value('expired_at');
    }

    // Trả về trạng thái hạn sử dụng để hiển thị.
    public function expiryStatusLabel()
    {
        if ($this->stock <= 0) {
            return 'Hết hàng';
        }

        if ($this->expiredQuantity() > 0) {
            return 'Có hàng hết hạn';
        }

        if ($this->nearExpiryQuantity() > 0) {
            return 'Cận date';
        }

        return 'Còn hạn';
    }

    // Trừ tồn kho khi khách đặt hàng.
    public function consumeStock($quantity, $includeExpired = false)
    {
        if ($quantity <= 0) {
            return [];
        }

        if ($includeExpired) {
            $available = $this->stock;
        } else {
            $available = $this->sellableStock();
        }

        if ($available < $quantity) {
            throw new \Exception('Sản phẩm "'.$this->display_name.'" không đủ hàng.');
        }

        $allocations = $this->consumeWarehouseStocks($quantity, $includeExpired);

        $this->stock -= $quantity;
        $this->refreshStatus();
        $this->save();

        return $allocations;
    }

    // Hoàn tồn kho khi đơn bị hủy hoặc cần trả lại hàng.
    public function restoreStock($quantity, $allocations = [])
    {
        if ($quantity <= 0) {
            return;
        }

        foreach ($allocations as $allocation) {
            $stockId = null;
            $itemId = null;
            $restoreQuantity = 0;

            if (isset($allocation['warehouse_stock_id'])) {
                $stockId = $allocation['warehouse_stock_id'];
            }

            if (isset($allocation['import_receipt_item_id'])) {
                $itemId = $allocation['import_receipt_item_id'];
            }

            if (isset($allocation['quantity'])) {
                $restoreQuantity = (int) $allocation['quantity'];
            }

            if ($restoreQuantity <= 0) {
                continue;
            }

            $stock = null;
            if ($stockId) {
                $stock = WarehouseStock::lockForUpdate()->find($stockId);
            } else {
                $stock = WarehouseStock::where('import_receipt_item_id', $itemId)->lockForUpdate()->first();
            }

            if ($stock && (int) $stock->product_id === (int) $this->id) {
                $stock->quantity_remaining += $restoreQuantity;
                $stock->save();
            }
        }

        $this->stock += $quantity;
        $this->refreshStatus();
        $this->save();
    }

    // Cập nhật trạng thái còn hàng hoặc hết hàng.
    public function refreshStatus()
    {
        if ($this->stock > 0 && $this->sellableStock() > 0) {
            $this->status = 'int_stock';
        } else {
            $this->status = 'out_of_stock';
        }
    }

    // Trả về chữ trạng thái tồn kho.
    public function statusLabel()
    {
        if ($this->sellableStock() > 0) {
            return 'Còn hàng';
        }

        return 'Hết hàng';
    }

    // Trừ số lượng trong từng lô kho.
    private function consumeWarehouseStocks($quantity, $includeExpired)
    {
        $remaining = $quantity;
        $allocations = [];

        $query = $this->warehouseStocks()
            ->where('quantity_remaining', '>', 0);

        if (! $includeExpired) {
            $query->where(function ($builder) {
                $builder->whereNull('expired_at')
                    ->orWhereDate('expired_at', '>=', today());
            });
        }

        $items = $query
            ->orderBy('expired_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($items as $item) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($remaining, (int) $item->quantity_remaining);
            if ($take <= 0) {
                continue;
            }

            $item->quantity_remaining -= $take;
            $item->save();

            $allocations[] = [
                'warehouse_stock_id' => $item->id,
                'import_receipt_item_id' => $item->import_receipt_item_id,
                'quantity' => $take,
                'price' => $this->priceForLot($item),
            ];

            $remaining -= $take;
        }

        if ($remaining > 0) {
            $allocations[] = [
                'import_receipt_item_id' => null,
                'quantity' => $remaining,
            ];
        }

        return $allocations;
    }

    // Tìm ảnh thay thế từ sản phẩm cùng biến thể nếu sản phẩm hiện tại chưa có ảnh.
    private function variantFallbackImage()
    {
        if (! $this->category_id) {
            return null;
        }

        $baseName = mb_strtolower($this->base_name, 'UTF-8');

        $products = self::with('firstImage')
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->get();

        foreach ($products as $product) {
            if ($product->firstImage && mb_strtolower($product->base_name, 'UTF-8') == $baseName) {
                return $product->firstImage;
            }
        }

        return null;
    }
}
