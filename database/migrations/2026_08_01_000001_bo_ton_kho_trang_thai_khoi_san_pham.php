<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Bo ton kho va trang thai khoi san_pham vi so luong da quan ly theo lo_hang_kho.
    public function up()
    {
        if (! Schema::hasColumn('san_pham', 'ton_kho') && ! Schema::hasColumn('san_pham', 'trang_thai')) {
            return;
        }

        $this->xoaIndexTonKhoSanPham();

        Schema::table('san_pham', function (Blueprint $table) {
            if (Schema::hasColumn('san_pham', 'ton_kho')) {
                $table->dropColumn('ton_kho');
            }

            if (Schema::hasColumn('san_pham', 'trang_thai')) {
                $table->dropColumn('trang_thai');
            }
        });
    }

    // Khoi phuc cot cu khi can rollback.
    public function down()
    {
        Schema::table('san_pham', function (Blueprint $table) {
            if (! Schema::hasColumn('san_pham', 'ton_kho')) {
                $table->unsignedInteger('ton_kho')->default(0)->after('gia');
            }

            if (! Schema::hasColumn('san_pham', 'trang_thai')) {
                $table->enum('trang_thai', ['con_hang', 'het_hang'])
                    ->default('het_hang')
                    ->after('ton_kho');
            }
        });

        $this->taoIndexTonKhoSanPham();
    }

    // Xoa index cu neu database dang co.
    private function xoaIndexTonKhoSanPham()
    {
        $indexes = DB::select('SHOW INDEX FROM san_pham');

        foreach ($indexes as $index) {
            if (
                $index->Key_name == 'products_status_stock_index'
                || $index->Key_name == 'san_pham_trang_thai_ton_kho_index'
            ) {
                DB::statement('ALTER TABLE san_pham DROP INDEX `'.$index->Key_name.'`');
                return;
            }
        }
    }

    // Tao lai index cu neu rollback.
    private function taoIndexTonKhoSanPham()
    {
        $indexes = DB::select('SHOW INDEX FROM san_pham');

        foreach ($indexes as $index) {
            if ($index->Key_name == 'products_status_stock_index') {
                return;
            }
        }

        if (Schema::hasColumn('san_pham', 'ton_kho') && Schema::hasColumn('san_pham', 'trang_thai')) {
            Schema::table('san_pham', function (Blueprint $table) {
                $table->index(['trang_thai', 'ton_kho'], 'products_status_stock_index');
            });
        }
    }
};