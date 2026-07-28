<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_stock_id')->nullable()->constrained('warehouse_stocks')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->text('reason');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'occurred_at']);
        });

        Schema::create('warehouse_damage_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_damage_id')->constrained('warehouse_damages')->cascadeOnDelete();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('media_type', 20)->default('image');
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
        });

        $this->moveExistingWarehouseDamages();
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_damage_media');
        Schema::dropIfExists('warehouse_damages');
    }

    private function moveExistingWarehouseDamages(): void
    {
        if (! Schema::hasTable('damage_slips') || ! Schema::hasTable('damage_slip_items')) {
            return;
        }

        $slips = DB::table('damage_slips')
            ->whereIn('source', ['warehouse_damage', 'lot_adjustment'])
            ->orderBy('id')
            ->get();

        foreach ($slips as $slip) {
            $items = DB::table('damage_slip_items')
                ->where('damage_slip_id', $slip->id)
                ->get();
            $media = Schema::hasTable('damage_slip_media')
                ? DB::table('damage_slip_media')->where('damage_slip_id', $slip->id)->get()
                : collect();

            foreach ($items as $item) {
                $product = DB::table('products')->where('id', $item->product_id)->first();
                $productName = $product
                    ? trim($product->name.' '.($product->unit ?? ''))
                    : 'Sản phẩm đã xóa';

                $damageId = DB::table('warehouse_damages')->insertGetId([
                    'warehouse_stock_id' => null,
                    'product_id' => $item->product_id,
                    'product_name' => $productName,
                    'quantity' => $item->quantity,
                    'reason' => $slip->reason,
                    'created_by' => $slip->created_by,
                    'occurred_at' => $slip->occurred_at,
                    'created_at' => $slip->created_at,
                    'updated_at' => $slip->updated_at,
                ]);

                foreach ($media as $file) {
                    DB::table('warehouse_damage_media')->insert([
                        'warehouse_damage_id' => $damageId,
                        'disk' => $file->disk,
                        'path' => $file->path,
                        'original_name' => $file->original_name,
                        'mime_type' => $file->mime_type,
                        'media_type' => $file->media_type,
                        'size' => $file->size,
                        'created_at' => $file->created_at,
                        'updated_at' => $file->updated_at,
                    ]);
                }
            }

            DB::table('damage_slips')->where('id', $slip->id)->delete();
        }
    }
};
