<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('import_receipt_items') || ! Schema::hasTable('warehouse_stocks')) {
            return;
        }

        DB::table('import_receipt_items')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('import_receipts')
                    ->whereColumn('import_receipts.id', 'import_receipt_items.import_receipt_id');
            })
            ->delete();

        $hasQuantityRemaining = Schema::hasColumn('import_receipt_items', 'quantity_remaining');
        $hasSalePrice = Schema::hasColumn('import_receipt_items', 'sale_price');

        $columns = [
            'import_receipt_items.id as import_receipt_item_id',
            'import_receipt_items.import_receipt_id',
            'import_receipt_items.product_id',
            'import_receipts.supplier_id',
            'import_receipt_items.quantity',
            'import_receipt_items.unit_price',
            'import_receipt_items.manufactured_at',
            'import_receipt_items.expired_at',
            'import_receipt_items.created_at',
            'import_receipt_items.updated_at',
        ];

        if ($hasQuantityRemaining) {
            $columns[] = 'import_receipt_items.quantity_remaining';
        }

        if ($hasSalePrice) {
            $columns[] = 'import_receipt_items.sale_price';
        }

        $items = DB::table('import_receipt_items')
            ->join('import_receipts', 'import_receipts.id', '=', 'import_receipt_items.import_receipt_id')
            ->leftJoin('warehouse_stocks', 'warehouse_stocks.import_receipt_item_id', '=', 'import_receipt_items.id')
            ->whereNull('warehouse_stocks.id')
            ->select($columns)
            ->orderBy('import_receipt_items.id')
            ->get();

        foreach ($items as $item) {
            DB::table('warehouse_stocks')->insert([
                'import_receipt_item_id' => $item->import_receipt_item_id,
                'import_receipt_id' => $item->import_receipt_id,
                'product_id' => $item->product_id,
                'supplier_id' => $item->supplier_id,
                'quantity' => $item->quantity,
                'quantity_remaining' => $hasQuantityRemaining ? $item->quantity_remaining : $item->quantity,
                'unit_price' => $item->unit_price,
                'sale_price' => $hasSalePrice ? $item->sale_price : null,
                'manufactured_at' => $item->manufactured_at,
                'expired_at' => $item->expired_at,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        // Không rollback dữ liệu dọn dẹp vì đây là dữ liệu mồ côi/không còn liên kết phiếu nhập.
    }
};
