<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamageSlip;
use App\Models\ImportReceipt;
use App\Models\ImportReceiptItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\WarehouseStock;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
    protected $cloudinary;

    // Gắn service upload ảnh/video minh chứng hàng lỗi khi nhập.
    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    // Hiển thị danh sách phiếu đặt mua.
    public function index()
    {
        $orders = PurchaseOrder::with(['supplier', 'items.product'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.pages.purchase-orders', compact('orders'));
    }

    // Hiển thị form tạo phiếu đặt mua.
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->orderBy('unit')->get();
        $productOptions = [];
        $code = PurchaseOrder::generateCode();

        foreach ($products as $product) {
            $productOptions[] = [
                'id' => $product->id,
                'name' => $product->display_name,
            ];
        }

        return view('admin.pages.purchase-order-add', compact('suppliers', 'products', 'productOptions', 'code'));
    }

    // Lưu phiếu đặt mua mới.
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'ordered_at' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
        ], [
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm.',
        ]);

        if ($this->hasDuplicateProduct($data['items'])) {
            return back()->withInput()->with('error', 'Mỗi sản phẩm chỉ được xuất hiện một lần trong phiếu đặt mua.');
        }

        $purchaseOrder = DB::transaction(function () use ($data) {
            $purchaseOrder = PurchaseOrder::create([
                'code' => PurchaseOrder::generateCode(),
                'supplier_id' => $data['supplier_id'],
                'status' => 'pending',
                'note' => null,
                'ordered_at' => $data['ordered_at'],
            ]);

            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => (int) $item['quantity_ordered'],
                ]);
            }

            return $purchaseOrder;
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Đã tạo phiếu đặt mua '.$purchaseOrder->code.'.');
    }

    // Hiển thị chi tiết phiếu đặt mua.
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'items.product',
            'importReceipts.items.product',
            'damageSlips.items.product',
            'damageSlips.mediaFiles',
        ])->findOrFail($id);

        return view('admin.pages.purchase-order-detail', compact('purchaseOrder'));
    }

    // Hiển thị form nhập hàng cho phiếu đặt mua.
    public function showImportForm($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);

        if ($purchaseOrder->status != 'pending') {
            return redirect()->route('admin.purchase-orders.index')
                ->with('error', 'Phiếu này đã được xử lý.');
        }

        return view('admin.pages.purchase-order-import', compact('purchaseOrder'));
    }

    // Xử lý nhập hàng, tạo phiếu nhập và ghi nhận hàng lỗi nếu có.
    public function processImport(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::with('items.product')->findOrFail($id);
        if ($purchaseOrder->status != 'pending') {
            return redirect()->route('admin.purchase-orders.index')->with('error', 'Phiếu này đã được xử lý.');
        }

        $data = $request->validate([
            'defect_description' => 'nullable|string|max:3000',
            'evidence' => 'nullable|array',
            'evidence.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.quantity_rejected' => 'required|integer|min:0',
            'items.*.manufactured_at' => 'nullable|date',
            'items.*.expired_at' => 'nullable|date',
        ]);

        $errors = $this->validateImportItems($purchaseOrder, $data['items']);
        $totalRejected = $this->totalRejectedQuantity($data['items']);
        $evidenceFiles = $this->evidenceFiles($request);

        if ($totalRejected > 0 && trim((string) $data['defect_description']) == '') {
            $errors['defect_description'] = 'Vui lòng nhập lý do hủy hàng lỗi/hư hỏng.';
        }

        if ($totalRejected > 0 && count($evidenceFiles) == 0) {
            $errors['evidence'] = 'Hàng lỗi/hư hỏng phải có ảnh hoặc video minh chứng.';
        }

        if (count($errors) > 0) {
            return back()->withInput()->withErrors($errors);
        }

        $storedPaths = [];

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::with('items.product')->lockForUpdate()->findOrFail($id);
            if ($purchaseOrder->status != 'pending') {
                throw new Exception('Phiếu đã được xử lý bởi phiên làm việc khác.');
            }

            $receipt = $this->createImportReceipt($purchaseOrder);
            $damageRows = $this->saveImportItems($purchaseOrder, $receipt, $data['items']);
            $media = [];

            if ($totalRejected > 0) {
                $media = $this->storeMedia($evidenceFiles, config('cloudinary.folders.damage_slips'), $storedPaths);
            }

            if (count($damageRows) > 0) {
                $damageSlip = $this->createDamageSlip($purchaseOrder, $receipt, $data['defect_description']);

                foreach ($damageRows as $row) {
                    $damageSlip->items()->create($row);
                }

                $this->saveDamageSlipMedia($damageSlip, $media);
            }

            $this->completePurchaseOrder($purchaseOrder, $totalRejected, $data['defect_description']);

            DB::commit();

            return redirect()->route('admin.import-receipts.show', $receipt)
                ->with('success', 'Đã nhập hàng và cập nhật tồn sản phẩm.');
        } catch (Exception $exception) {
            DB::rollBack();
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return back()->withInput()->with('error', 'Không thể nhập hàng: '.$exception->getMessage());
        }
    }

    // Hiển thị danh sách phiếu nhập hàng.
    public function importReceipts()
    {
        $receipts = ImportReceipt::with(['supplier', 'purchaseOrder', 'items.product'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.pages.import-receipts', compact('receipts'));
    }

    // Hiển thị chi tiết phiếu nhập hàng.
    public function showImportReceipt(ImportReceipt $receipt)
    {
        $receipt->load(['supplier', 'purchaseOrder', 'items.product']);

        return view('admin.pages.import-receipt-detail', compact('receipt'));
    }

    // Hiển thị danh sách phiếu hàng lỗi.
    public function damageSlips()
    {
        $damageSlips = DamageSlip::with(['supplier', 'purchaseOrder', 'importReceipt', 'items.product'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.pages.damage-slips', compact('damageSlips'));
    }

    // Hiển thị chi tiết phiếu hàng lỗi.
    public function showDamageSlip(DamageSlip $damageSlip)
    {
        $damageSlip->load(['supplier', 'purchaseOrder', 'importReceipt', 'creator', 'items.product', 'mediaFiles']);

        return view('admin.pages.damage-slip-detail', compact('damageSlip'));
    }

    // Xóa phiếu đặt mua nếu chưa có phiếu nhập.
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::with('importReceipts')->findOrFail($id);
        if ($purchaseOrder->importReceipts()->exists()) {
            return back()->with('error', 'Không thể xóa phiếu đã có phiếu nhập hàng.');
        }

        $purchaseOrder->delete();

        return redirect()->route('admin.purchase-orders.index')->with('success', 'Đã xóa phiếu đặt mua.');
    }

    // Kiểm tra sản phẩm có bị chọn trùng trong phiếu đặt mua không.
    private function hasDuplicateProduct($items)
    {
        $productIds = [];

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            if (in_array($productId, $productIds)) {
                return true;
            }

            $productIds[] = $productId;
        }

        return false;
    }

    // Lấy danh sách file minh chứng hợp lệ từ request.
    private function evidenceFiles($request)
    {
        $evidenceFiles = [];
        $files = $request->file('evidence', []);

        foreach ($files as $file) {
            if ($file) {
                $evidenceFiles[] = $file;
            }
        }

        return $evidenceFiles;
    }

    // Tính tổng số lượng hàng bị hủy do lỗi khi nhập.
    private function totalRejectedQuantity($items)
    {
        $total = 0;

        foreach ($items as $item) {
            $total += (int) $item['quantity_rejected'];
        }

        return $total;
    }

    // Kiểm tra từng dòng nhập hàng trước khi lưu.
    private function validateImportItems($purchaseOrder, $itemInputs)
    {
        $errors = [];
        $inputIds = $this->inputItemIds($itemInputs);
        $expectedIds = $this->purchaseOrderItemIds($purchaseOrder);

        sort($inputIds);
        sort($expectedIds);

        if ($inputIds != $expectedIds) {
            return ['items' => 'Danh sách nhập phải đúng sản phẩm của phiếu đặt mua.'];
        }

        foreach ($itemInputs as $index => $input) {
            $item = $this->findPurchaseOrderItem($purchaseOrder, (int) $input['item_id']);
            $received = (int) $input['quantity_received'];
            $rejected = (int) $input['quantity_rejected'];
            $imported = $received - $rejected;
            $manufacturedAt = '';
            $expiredAt = '';

            if (isset($input['manufactured_at'])) {
                $manufacturedAt = $input['manufactured_at'];
            }
            if (isset($input['expired_at'])) {
                $expiredAt = $input['expired_at'];
            }

            if (! $item) {
                $errors['items.'.$index.'.item_id'] = 'Sản phẩm nhập không thuộc phiếu đặt mua.';
                continue;
            }

            if ($received > $item->quantity_ordered) {
                $errors['items.'.$index.'.quantity_received'] = 'Số nhận không được lớn hơn số đặt mua.';
            }
            if ($rejected > $received) {
                $errors['items.'.$index.'.quantity_rejected'] = 'Số hủy không được lớn hơn số nhận.';
            }
            if ($imported < 0) {
                $errors['items.'.$index.'.quantity_rejected'] = 'Số hủy không hợp lệ.';
            }
            if ($imported > 0 && trim($manufacturedAt) == '') {
                $errors['items.'.$index.'.manufactured_at'] = 'Vui lòng nhập ngày sản xuất/đóng gói.';
            }
            if ($imported > 0 && trim($expiredAt) == '') {
                $errors['items.'.$index.'.expired_at'] = 'Vui lòng nhập hạn sử dụng.';
            }
            if ($imported > 0 && trim($expiredAt) != '' && $expiredAt < now()->toDateString()) {
                $errors['items.'.$index.'.expired_at'] = 'Hàng hết hạn không được nhập kho bán.';
            }
            if ($imported > 0 && trim($manufacturedAt) != '' && trim($expiredAt) != '' && $expiredAt < $manufacturedAt) {
                $errors['items.'.$index.'.expired_at'] = 'Hạn sử dụng phải sau ngày sản xuất/đóng gói.';
            }
        }

        return $errors;
    }

    // Lấy id dòng nhập do form gửi lên.
    private function inputItemIds($itemInputs)
    {
        $ids = [];

        foreach ($itemInputs as $input) {
            $ids[] = (int) $input['item_id'];
        }

        return $ids;
    }

    // Lấy id các dòng sản phẩm thuộc phiếu đặt mua.
    private function purchaseOrderItemIds($purchaseOrder)
    {
        $ids = [];

        foreach ($purchaseOrder->items as $item) {
            $ids[] = (int) $item->id;
        }

        return $ids;
    }

    // Tìm dòng sản phẩm trong phiếu đặt mua theo id.
    private function findPurchaseOrderItem($purchaseOrder, $itemId)
    {
        foreach ($purchaseOrder->items as $item) {
            if ($item->id == $itemId) {
                return $item;
            }
        }

        return null;
    }

    // Tạo phiếu nhập hàng từ phiếu đặt mua.
    private function createImportReceipt($purchaseOrder)
    {
        return ImportReceipt::create([
            'code' => ImportReceipt::generateCode(),
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id' => $purchaseOrder->supplier_id,
            'received_at' => now(),
            'note' => null,
        ]);
    }

    // Lưu từng dòng nhập hàng, cộng tồn kho và trả danh sách hàng lỗi nếu có.
    private function saveImportItems($purchaseOrder, $receipt, $itemInputs)
    {
        $damageRows = [];

        foreach ($itemInputs as $input) {
            $item = $this->findPurchaseOrderItem($purchaseOrder, (int) $input['item_id']);
            if (! $item) {
                continue;
            }

            $received = (int) $input['quantity_received'];
            $rejected = (int) $input['quantity_rejected'];
            $imported = $received - $rejected;
            $manufacturedAt = null;
            $expiredAt = null;

            if ($imported > 0 && isset($input['manufactured_at'])) {
                $manufacturedAt = $input['manufactured_at'];
            }
            if ($imported > 0 && isset($input['expired_at'])) {
                $expiredAt = $input['expired_at'];
            }

            $item->update([
                'quantity_received' => $received,
                'quantity_rejected' => $rejected,
                'quantity_imported' => $imported,
                'manufactured_at' => $manufacturedAt,
                'expired_at' => $expiredAt,
            ]);

            if ($imported > 0) {
                $receiptItem = ImportReceiptItem::create([
                    'import_receipt_id' => $receipt->id,
                    'purchase_order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $imported,
                    'manufactured_at' => $manufacturedAt,
                    'expired_at' => $expiredAt,
                ]);

                WarehouseStock::create([
                    'import_receipt_item_id' => $receiptItem->id,
                    'import_receipt_id' => $receipt->id,
                    'product_id' => $item->product_id,
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'quantity' => $imported,
                    'quantity_remaining' => $imported,
                    'manufactured_at' => $manufacturedAt,
                    'expired_at' => $expiredAt,
                ]);

                $this->increaseProductStock($item->product_id, $imported);
            }

            if ($rejected > 0) {
                $damageRows[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $rejected,
                    'note' => 'Hàng lỗi khi nhập từ phiếu '.$purchaseOrder->code,
                ];
            }
        }

        return $damageRows;
    }

    // Tạo phiếu ghi nhận hàng lỗi trong lúc nhập hàng.
    private function createDamageSlip($purchaseOrder, $receipt, $reason)
    {
        return DamageSlip::create([
            'code' => DamageSlip::generateCode(),
            'purchase_order_id' => $purchaseOrder->id,
            'import_receipt_id' => $receipt->id,
            'supplier_id' => $purchaseOrder->supplier_id,
            'created_by' => $this->adminId(),
            'reason' => $reason,
            'occurred_at' => now(),
        ]);
    }

    // Hoàn tất phiếu đặt mua sau khi nhập hàng.
    private function completePurchaseOrder($purchaseOrder, $totalRejected, $defectDescription)
    {
        $purchaseOrder->update([
            'status' => 'completed',
            'received_at' => now(),
            'defect_description' => $totalRejected > 0 ? $defectDescription : null,
            'supplier_reported_at' => $totalRejected > 0 ? now() : null,
        ]);
    }

    // Cộng tồn kho tổng của sản phẩm.
    private function increaseProductStock($productId, $quantity)
    {
        $product = Product::lockForUpdate()->findOrFail($productId);
        $product->stock = $product->stock + $quantity;
        $product->refreshStatus();
        $product->save();
    }

    // Upload các file minh chứng và lưu đường dẫn đã upload.
    private function storeMedia($files, $directory, &$storedPaths)
    {
        return $this->cloudinary->uploadMany($files, $directory, $storedPaths);
    }

    // Lưu ảnh/video minh chứng vào phiếu hàng lỗi.
    private function saveDamageSlipMedia($damageSlip, $media)
    {
        foreach ($media as $item) {
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

            $damageSlip->mediaFiles()->create([
                'disk' => $disk,
                'path' => $path,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'media_type' => $mediaType,
                'size' => $size,
            ]);
        }
    }

    // Lấy id admin đang đăng nhập để lưu người tạo phiếu.
    private function adminId()
    {
        $adminId = Auth::guard('admin')->id();
        if ($adminId) {
            return $adminId;
        }

        return Auth::id();
    }
}
