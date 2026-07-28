<?php

namespace Database\Seeders;

use App\Models\DamageSlip;
use App\Models\ImportReceipt;
use App\Models\ImportReceiptItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WarehouseDamage;
use App\Models\WarehouseStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $products = Product::orderBy('category_id')->orderBy('name')->orderBy('unit')->get();

        if ($products->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($admin, $products) {
            $supplierA = Supplier::updateOrCreate(
                ['name' => 'Hợp tác xã Nông sản Tây Nguyên'],
                ['phone' => '0901234567', 'description' => 'Nguồn hàng gia vị, hạt và nông sản khô.']
            );

            $supplierB = Supplier::updateOrCreate(
                ['name' => 'Vựa Trái Cây Miền Tây'],
                ['phone' => '0912345678', 'description' => 'Nhà cung cấp gạo và thực phẩm đóng gói.']
            );

            $this->resetSeededWorkflowData();
            Product::query()->update(['stock' => 0, 'status' => 'out_of_stock']);

            $this->createCompletedImport($products, $supplierA, $admin);
            $this->createPendingPurchaseOrder($products->take(3), $supplierB, $admin);
            $this->createWarehouseDamage($products->first(), $admin);
        });
    }

    private function createCompletedImport($products, Supplier $supplier, ?User $admin): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'code' => 'PO-0001',
            'supplier_id' => $supplier->id,
            'status' => 'completed',
            'note' => 'Phiếu đặt mua tạo tồn ban đầu cho sản phẩm.',
            'ordered_at' => now()->subDays(4)->toDateString(),
            'received_at' => now()->subDays(3),
        ]);

        $receipt = ImportReceipt::create([
            'code'              => 'IR-0001',
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id'       => $supplier->id,
            'received_at'       => now()->subDays(3),
            'note'              => 'Nhập hàng, stock sản phẩm được cộng từ phiếu này.',
        ]);

        $damageRows = [];

        foreach ($products as $index => $product) {
            $ordered = 25;
            $received = 25;
            $rejected = in_array($index, [1, 8, 17], true) ? 2 : 0;
            $imported = $received - $rejected;
            $isNearExpiry = in_array($index, [0, 5, 10, 15], true);
            $expiredAt = $isNearExpiry
                ? now()->addDays(45)->toDateString()
                : now()->addMonths(10)->toDateString();

            $item = PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id'        => $product->id,
                'quantity_ordered'  => $ordered,
                'quantity_received' => $received,
                'quantity_rejected' => $rejected,
                'quantity_imported' => $imported,
                'manufactured_at'   => now()->subDays(20)->toDateString(),
                'expired_at'        => $expiredAt,
            ]);

            $receiptItem = ImportReceiptItem::create([
                'import_receipt_id'      => $receipt->id,
                'purchase_order_item_id' => $item->id,
                'product_id'             => $product->id,
                'quantity'               => $imported,
                'manufactured_at'        => $item->manufactured_at,
                'expired_at'             => $item->expired_at,
            ]);

            WarehouseStock::create([
                'import_receipt_item_id' => $receiptItem->id,
                'import_receipt_id' => $receipt->id,
                'product_id' => $product->id,
                'supplier_id' => $supplier->id,
                'quantity' => $imported,
                'quantity_remaining' => $imported,
                'unit_price' => $item->unit_price,
                'sale_price' => $isNearExpiry ? round((float) $product->price * 0.85, -3) : null,
                'manufactured_at' => $item->manufactured_at,
                'expired_at' => $item->expired_at,
            ]);

            $product->stock = $imported;
            $product->refreshStatus();
            $product->save();

            if ($rejected > 0) {
                $damageRows[] = [
                    'product_id' => $product->id,
                    'quantity' => $rejected,
                    'note' => 'Hàng lỗi khi kiểm nhận từ nhà cung cấp.',
                ];
            }
        }

        if ($damageRows !== []) {
            $damageSlip = DamageSlip::create([
                'code'              => 'DS-0001',
                'purchase_order_id' => $purchaseOrder->id,
                'import_receipt_id' => $receipt->id,
                'supplier_id'       => $supplier->id,
                'created_by'        => $admin?->id,
                'reason'            => 'Một số sản phẩm bị móp, rách bao bì khi nhận hàng nên không cộng vào stock.',
                'occurred_at'       => now()->subDays(3),
            ]);

            foreach ($damageRows as $row) {
                $damageSlip->items()->create($row);
            }

        }
    }

    private function createPendingPurchaseOrder($products, Supplier $supplier, ?User $admin): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'code' => 'PO-0002',
            'supplier_id' => $supplier->id,
            'status' => 'pending',
            'note' => 'Phiếu đặt mua đang chờ nhập hàng.',
            'ordered_at' => now()->toDateString(),
        ]);

        foreach ($products as $product) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id'        => $product->id,
                'quantity_ordered'  => 12,
            ]);
        }
    }

    private function createWarehouseDamage(?Product $product, ?User $admin): void
    {
        $stock = $product
            ? WarehouseStock::where('product_id', $product->id)
                ->where('quantity_remaining', '>=', 2)
                ->first()
            : null;

        if (! $product || ! $stock || $product->stock < 2) {
            return;
        }

        $product->consumeStock(2, true);
        $stock->decrement('quantity_remaining', 2);

        WarehouseDamage::create([
            'warehouse_stock_id' => $stock->id,
            'product_id' => $product->id,
            'product_name' => $product->display_name,
            'quantity' => 2,
            'reason' => 'Hàng bị rách bao bì trong quá trình trưng bày, ghi nhận hủy và trừ stock.',
            'occurred_at' => now()->subDay(),
        ]);
    }

    private function resetSeededWorkflowData(): void
    {
        DamageSlip::where('code', 'DS-0001')->delete();
        WarehouseDamage::where('reason', 'Hàng bị rách bao bì trong quá trình trưng bày, ghi nhận hủy và trừ stock.')->delete();
        ImportReceipt::whereIn('code', ['IR-0001'])->delete();
        PurchaseOrder::whereIn('code', ['PO-0001', 'PO-0002'])->delete();
    }
}
