<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('wishlists', 'inventory_id')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->dropForeign(['inventory_id']);
                $table->dropColumn('inventory_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('wishlists', 'inventory_id')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->foreignId('inventory_id')->nullable()->constrained('inventories')->nullOnDelete();
            });
        }
    }
};
