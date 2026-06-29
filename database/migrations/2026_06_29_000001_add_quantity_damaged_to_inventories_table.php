<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventories')) {
            return;
        }

        if (!Schema::hasColumn('inventories', 'quantity_damaged')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->integer('quantity_damaged')->default(0)->after('quantity_remaining');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('inventories')) {
            return;
        }

        if (Schema::hasColumn('inventories', 'quantity_damaged')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropColumn('quantity_damaged');
            });
        }
    }
};
