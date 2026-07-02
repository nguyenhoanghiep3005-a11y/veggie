<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cart_items', 'inventory_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreignId('inventory_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('inventories')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cart_items', 'inventory_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('inventory_id');
            });
        }
    }
};