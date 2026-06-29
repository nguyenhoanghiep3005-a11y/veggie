<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventories') && !Schema::hasColumn('inventories', 'damaged_item_numbers')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->text('damaged_item_numbers')->nullable()->after('quantity_damaged');
            });

            $inventories = DB::table('inventories')
                ->where('quantity_damaged', '>', 0)
                ->get();

            foreach ($inventories as $inventory) {
                $soldQuantity = $inventory->quantity_imported - $inventory->quantity_remaining - $inventory->quantity_damaged;
                $damagedItemNumbers = [];

                if ($soldQuantity < 0) {
                    $soldQuantity = 0;
                }

                for ($i = $inventory->quantity_imported; $i >= 1; $i--) {
                    if (count($damagedItemNumbers) >= $inventory->quantity_damaged) {
                        break;
                    }

                    if ($i > $soldQuantity) {
                        $damagedItemNumbers[] = $i;
                    }
                }

                sort($damagedItemNumbers);

                DB::table('inventories')
                    ->where('id', $inventory->id)
                    ->update([
                        'damaged_item_numbers' => count($damagedItemNumbers) > 0 ? implode(',', $damagedItemNumbers) : null,
                    ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventories') && Schema::hasColumn('inventories', 'damaged_item_numbers')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropColumn('damaged_item_numbers');
            });
        }
    }
};
