<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tach khoi luong khoi don vi de co the nhap va quan ly rieng.
    public function up()
    {
        Schema::table('san_pham', function (Blueprint $table) {
            $table->decimal('khoi_luong', 10, 3)->nullable()->after('gia');
        });

        $sanPhams = DB::table('san_pham')
            ->select(['ma_san_pham', 'ten', 'duong_dan', 'don_vi'])
            ->get();

        foreach ($sanPhams as $sanPham) {
            $tenGoc = trim((string) $sanPham->ten);
            $timThayTrongTen = preg_match(
                '/(\d+(?:[,.]\d+)?)\s*(g|gram|kg)$/iu',
                $tenGoc,
                $ketQua
            );

            if ($timThayTrongTen) {
                $tenGoc = trim(preg_replace(
                    '/\s*'.preg_quote($ketQua[0], '/').'$/iu',
                    '',
                    $tenGoc
                ));
            } elseif (preg_match(
                '/-(\d+(?:-\d+)?)(g|kg)-\d+$/i',
                (string) $sanPham->duong_dan,
                $ketQua
            )) {
                $ketQua[1] = str_replace('-', '.', $ketQua[1]);
            } else {
                continue;
            }

            $khoiLuong = (float) str_replace(',', '.', $ketQua[1]);
            $donVi = strtolower($ketQua[2]) == 'kg' ? 'kg' : 'g';

            DB::table('san_pham')
                ->where('ma_san_pham', $sanPham->ma_san_pham)
                ->update([
                    'ten' => $tenGoc,
                    'khoi_luong' => $khoiLuong,
                    'don_vi' => $donVi,
                ]);
        }
    }

    public function down()
    {
        Schema::table('san_pham', function (Blueprint $table) {
            $table->dropColumn('khoi_luong');
        });
    }
};
