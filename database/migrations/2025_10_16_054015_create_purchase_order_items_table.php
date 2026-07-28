<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('quantity_ordered');
            $table->unsignedInteger('quantity_received')->default(0);
            $table->unsignedInteger('quantity_rejected')->default(0);
            $table->unsignedInteger('quantity_imported')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->date('manufactured_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->timestamps();
            $table->unique(['purchase_order_id', 'product_id']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE purchase_order_items ADD CONSTRAINT po_items_quantities_valid CHECK (quantity_ordered > 0 AND quantity_received <= quantity_ordered AND quantity_rejected <= quantity_received AND quantity_imported + quantity_rejected = quantity_received)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
