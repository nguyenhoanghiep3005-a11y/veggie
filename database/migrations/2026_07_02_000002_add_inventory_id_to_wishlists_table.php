<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('wishlists', 'inventory_id')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->foreignId('inventory_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('inventories')
                    ->nullOnDelete();

                $table->index(['user_id', 'product_id', 'inventory_id'], 'wishlists_user_product_inventory_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('wishlists', 'inventory_id')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->dropIndex('wishlists_user_product_inventory_index');
                $table->dropConstrainedForeignId('inventory_id');
            });
        }
    }
};
