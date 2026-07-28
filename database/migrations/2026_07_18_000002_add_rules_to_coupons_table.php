<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('minimum_order_amount', 12, 2)->default(0)->after('discount_percent');
            $table->decimal('max_discount_amount', 12, 2)->nullable()->after('minimum_order_amount');
            $table->string('apply_type', 20)->default('all')->after('used_count');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'minimum_order_amount',
                'max_discount_amount',
                'apply_type',
            ]);
        });
    }
};
