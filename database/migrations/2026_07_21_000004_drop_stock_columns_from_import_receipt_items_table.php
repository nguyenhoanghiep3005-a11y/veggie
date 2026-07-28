<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('import_receipt_items')) {
            return;
        }

        $columns = [];

        if (Schema::hasColumn('import_receipt_items', 'quantity_remaining')) {
            $columns[] = 'quantity_remaining';
        }

        if (Schema::hasColumn('import_receipt_items', 'sale_price')) {
            $columns[] = 'sale_price';
        }

        if ($columns !== []) {
            Schema::table('import_receipt_items', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('import_receipt_items')) {
            return;
        }

        Schema::table('import_receipt_items', function (Blueprint $table) {
            if (! Schema::hasColumn('import_receipt_items', 'quantity_remaining')) {
                $table->unsignedInteger('quantity_remaining')->default(0)->after('quantity');
            }

            if (! Schema::hasColumn('import_receipt_items', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('unit_price');
            }
        });
    }
};
