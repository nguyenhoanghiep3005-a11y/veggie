<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Chuyen phan bo JSON thanh ma lo hang tren tung dong chi tiet don hang.
    public function up()
    {
        if (! Schema::hasColumn('chi_tiet_don_hang', 'ma_lo_hang_kho')) {
            Schema::table('chi_tiet_don_hang', function (Blueprint $table) {
                $table->foreignId('ma_lo_hang_kho')
                    ->nullable()
                    ->after('ma_san_pham')
                    ->constrained('lo_hang_kho', 'ma_lo_hang_kho')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasColumn('chi_tiet_don_hang', 'phan_bo_ton_kho')) {
            $this->chuyenDuLieuCu();

            Schema::table('chi_tiet_don_hang', function (Blueprint $table) {
                $table->dropColumn('phan_bo_ton_kho');
            });
        }
    }

    // Khoi phuc JSON khi rollback migration.
    public function down()
    {
        if (! Schema::hasColumn('chi_tiet_don_hang', 'phan_bo_ton_kho')) {
            Schema::table('chi_tiet_don_hang', function (Blueprint $table) {
                $table->json('phan_bo_ton_kho')->nullable()->after('gia');
            });
        }

        $chiTietDonHangs = DB::table('chi_tiet_don_hang')
            ->select(
                'ma_chi_tiet_don_hang',
                'ma_lo_hang_kho',
                'so_luong',
                'gia'
            )
            ->get();

        foreach ($chiTietDonHangs as $chiTietDonHang) {
            $phanBo = [];

            if ($chiTietDonHang->ma_lo_hang_kho) {
                $phanBo[] = [
                    'ma_lo_hang_kho' => (int) $chiTietDonHang->ma_lo_hang_kho,
                    'so_luong' => (int) $chiTietDonHang->so_luong,
                    'gia' => (float) $chiTietDonHang->gia,
                ];
            }

            DB::table('chi_tiet_don_hang')
                ->where(
                    'ma_chi_tiet_don_hang',
                    $chiTietDonHang->ma_chi_tiet_don_hang
                )
                ->update([
                    'phan_bo_ton_kho' => json_encode(
                        $phanBo,
                        JSON_UNESCAPED_UNICODE
                    ),
                ]);
        }

        if (Schema::hasColumn('chi_tiet_don_hang', 'ma_lo_hang_kho')) {
            Schema::table('chi_tiet_don_hang', function (Blueprint $table) {
                $table->dropForeign(['ma_lo_hang_kho']);
                $table->dropColumn('ma_lo_hang_kho');
            });
        }
    }

    // Giu lien ket neu lo hang cu van con ton tai va dung san pham.
    private function chuyenDuLieuCu()
    {
        $loHangKhos = DB::table('lo_hang_kho')
            ->select(
                'ma_lo_hang_kho',
                'ma_chi_tiet_phieu_nhap',
                'ma_san_pham'
            )
            ->get();
        $loHangTheoMa = $loHangKhos->keyBy('ma_lo_hang_kho');
        $loHangTheoChiTietNhap = $loHangKhos->keyBy('ma_chi_tiet_phieu_nhap');

        $chiTietDonHangs = DB::table('chi_tiet_don_hang')
            ->whereNotNull('phan_bo_ton_kho')
            ->select(
                'ma_chi_tiet_don_hang',
                'ma_san_pham',
                'so_luong',
                'phan_bo_ton_kho'
            )
            ->orderBy('ma_chi_tiet_don_hang')
            ->get();

        foreach ($chiTietDonHangs as $chiTietDonHang) {
            $cacPhanBo = json_decode(
                $chiTietDonHang->phan_bo_ton_kho,
                true
            );

            if (! is_array($cacPhanBo) || count($cacPhanBo) !== 1) {
                throw new \RuntimeException(
                    'Chi tiet don hang '
                    .$chiTietDonHang->ma_chi_tiet_don_hang
                    .' khong co dung mot lo hang.'
                );
            }

            $phanBo = $cacPhanBo[0];
            $soLuong = (int) ($phanBo['so_luong']
                ?? $phanBo['quantity']
                ?? 0);

            if ($soLuong !== (int) $chiTietDonHang->so_luong) {
                throw new \RuntimeException(
                    'So luong lo hang cua chi tiet don hang '
                    .$chiTietDonHang->ma_chi_tiet_don_hang
                    .' khong khop.'
                );
            }

            $maLoHangKho = $phanBo['ma_lo_hang_kho']
                ?? $phanBo['warehouse_stock_id']
                ?? null;
            $maChiTietPhieuNhap = $phanBo['ma_chi_tiet_phieu_nhap']
                ?? $phanBo['import_receipt_item_id']
                ?? null;
            $loHangKho = null;

            if ($maLoHangKho) {
                $loHangKho = $loHangTheoMa->get($maLoHangKho);
            }

            if (! $loHangKho && $maChiTietPhieuNhap) {
                $loHangKho = $loHangTheoChiTietNhap->get(
                    $maChiTietPhieuNhap
                );
            }

            if (
                $loHangKho
                && (int) $loHangKho->ma_san_pham
                    !== (int) $chiTietDonHang->ma_san_pham
            ) {
                $loHangKho = null;
            }

            DB::table('chi_tiet_don_hang')
                ->where(
                    'ma_chi_tiet_don_hang',
                    $chiTietDonHang->ma_chi_tiet_don_hang
                )
                ->update([
                    'ma_lo_hang_kho' => $loHangKho
                        ? $loHangKho->ma_lo_hang_kho
                        : null,
                ]);
        }
    }
};
