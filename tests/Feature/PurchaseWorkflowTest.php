<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DamageSlip;
use App\Models\ImportReceipt;
use App\Models\ImportReceiptItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\WarehouseDamage;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\WarehouseController;
use Tests\TestCase;

class PurchaseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_purchase_order_and_import_only_accepted_quantity(): void
    {
        $this->withoutMiddleware();
        Storage::fake('public');

        $supplier = Supplier::create([
            'name' => 'Nhà cung cấp kiểm thử',
            'phone' => '0900000000',
            'email' => 'supplier@example.test',
            'address' => 'TP.HCM',
            'description' => 'Dữ liệu kiểm thử',
            'status' => true,
        ]);

        $category = Category::create([
            'name' => 'Rau củ',
            'slug' => 'rau-cu',
            'status' => 1,
        ]);

        $product = Product::create([
            'name' => 'Cà chua',
            'slug' => 'ca-chua-500g',
            'category_id' => $category->id,
            'description' => 'Cà chua tươi',
            'price' => 25000,
            'stock' => 0,
            'status' => 'out_of_stock',
            'unit' => '500g',
        ]);

        $storeRequest = Request::create('/admin/purchase-orders', 'POST', [
            'supplier_id' => $supplier->id,
            'ordered_at' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_ordered' => 10,
                ],
            ],
        ]);
        $storeResponse = app(PurchaseOrderController::class)->store($storeRequest);

        $this->assertTrue($storeResponse->isRedirect());

        $purchaseOrder = PurchaseOrder::with('items')->firstOrFail();
        $purchaseItem = $purchaseOrder->items->first();

        $importRequest = Request::create('/admin/purchase-orders/'.$purchaseOrder->id.'/import', 'POST', [
            'defect_description' => 'Có 2 sản phẩm bị dập khi nhận hàng',
            'items' => [
                [
                    'item_id' => $purchaseItem->id,
                    'quantity_received' => 10,
                    'quantity_rejected' => 2,
                    'manufactured_at' => now()->toDateString(),
                    'expired_at' => now()->addDays(120)->toDateString(),
                ],
            ],
        ], [], [
            'evidence' => [
                UploadedFile::fake()->image('damage.png'),
            ],
        ]);
        $importResponse = app(PurchaseOrderController::class)->processImport($importRequest, $purchaseOrder->id);

        $this->assertTrue($importResponse->isRedirect());

        $product->refresh();
        $purchaseOrder->refresh();

        $this->assertSame('completed', $purchaseOrder->status);
        $this->assertSame(8, $product->stock);
        $this->assertSame('int_stock', $product->status);
        $this->assertSame(1, ImportReceipt::count());
        $this->assertSame(8, ImportReceiptItem::first()->quantity);
        $this->assertSame(8, WarehouseStock::first()->quantity_remaining);
        $this->assertSame(1, DamageSlip::count());
        $this->assertSame(2, DamageSlip::first()->totalQuantity());

        $stock = WarehouseStock::firstOrFail();
        $damageRequest = Request::create('/admin/warehouses/'.$stock->id.'/adjust', 'POST', [
            'action' => 'damage',
            'damage_quantity' => 2,
            'damage_reason' => 'Hàng bị hư trong quá trình bảo quản',
        ], [], [
            'evidence' => [
                UploadedFile::fake()->image('warehouse-damage.png'),
            ],
        ]);

        $damageResponse = app(WarehouseController::class)->adjust($damageRequest, $stock);

        $this->assertTrue($damageResponse->isRedirect());

        $product->refresh();
        $stock->refresh();
        $warehouseDamage = WarehouseDamage::firstOrFail();

        $this->assertSame(6, $product->stock);
        $this->assertSame(6, $stock->quantity_remaining);
        $this->assertSame(1, DamageSlip::count());
        $this->assertSame(2, $warehouseDamage->quantity);
        $this->assertSame('Hàng bị hư trong quá trình bảo quản', $warehouseDamage->reason);
        $this->assertSame(1, $warehouseDamage->mediaFiles()->count());
    }
}
