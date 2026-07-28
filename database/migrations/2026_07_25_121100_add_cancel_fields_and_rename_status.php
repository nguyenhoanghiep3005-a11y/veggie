<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add cancel tracking fields
        Schema::table('orders', function (Blueprint $table) {
            $table->string('canceled_by')->nullable()->after('status');
            $table->text('cancel_reason')->nullable()->after('canceled_by');
        });

        // 2. Rename processing → shipping for existing orders
        DB::table('orders')
            ->where('status', 'processing')
            ->update(['status' => 'shipping']);
    }

    public function down(): void
    {
        // Revert shipping → processing
        DB::table('orders')
            ->where('status', 'shipping')
            ->update(['status' => 'processing']);

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['canceled_by', 'cancel_reason']);
        });
    }
};
