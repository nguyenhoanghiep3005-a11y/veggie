<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_receipt_item_id')->unique()->constrained('import_receipt_items')->cascadeOnDelete();
            $table->foreignId('import_receipt_id')->constrained('import_receipts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('quantity_remaining')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->date('manufactured_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'quantity_remaining', 'expired_at']);
        });

        if (! Schema::hasTable('import_receipt_items')) {
            return;
        }

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
        Schema::dropIfExists('warehouse_stocks');
    }
};
