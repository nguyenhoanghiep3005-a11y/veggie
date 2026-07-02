<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create( 'inventories',function(Blueprint $table)
       {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity_imported');
            $table->integer('quantity_remaining');
            $table->integer('quantity_damaged')->default(0);
            $table->text('damaged_item_numbers')->nullable();
            $table->date('imported_at');
            $table->date('expired_at');
            $table->enum('condition', ['fresh', 'near_expiry', 'expired', 'damaged', 'sold_out'])->default('fresh');
            $table->decimal('adjusted_price',10,2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'condition']);
            $table->index('expired_at');
       });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
