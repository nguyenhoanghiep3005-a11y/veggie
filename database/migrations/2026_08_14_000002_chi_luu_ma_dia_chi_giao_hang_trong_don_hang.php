<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Chuyen ban chup dia chi JSON thanh ban ghi dia chi cua tung don hang.
    public function up()
    {
        if (Schema::hasColumn('don_hang', 'du_lieu_dia_chi_giao_hang')) {
            $this->chuyenDiaChiCuSangBangDiaChi();
        }

        if (DB::table('don_hang')->whereNull('ma_dia_chi_giao_hang')->exists()) {
            throw new RuntimeException(
                'Van con don hang chua co ma dia chi giao hang.'
            );
        }

        $this->xoaKhoaNgoaiDiaChi();

        Schema::table('don_hang', function (Blueprint $table) {
            $table->unsignedBigInteger('ma_dia_chi_giao_hang')
                ->nullable(false)
                ->change();
            $table->foreign(
                'ma_dia_chi_giao_hang',
                'don_hang_dia_chi_fk'
            )->references('ma_dia_chi_giao_hang')
                ->on('dia_chi_giao_hang')
                ->restrictOnDelete();
        });

        if (Schema::hasColumn('don_hang', 'du_lieu_dia_chi_giao_hang')) {
            Schema::table('don_hang', function (Blueprint $table) {
                $table->dropColumn('du_lieu_dia_chi_giao_hang');
            });
        }
    }

    // Khoi phuc cot JSON va khoa ngoai cu khi rollback.
    public function down()
    {
        $this->xoaKhoaNgoaiDiaChi();

        Schema::table('don_hang', function (Blueprint $table) {
            $table->unsignedBigInteger('ma_dia_chi_giao_hang')
                ->nullable()
                ->change();
            $table->foreign(
                'ma_dia_chi_giao_hang',
                'fk_don_hang_dia_chi'
            )->references('ma_dia_chi_giao_hang')
                ->on('dia_chi_giao_hang')
                ->nullOnDelete();
        });

        if (! Schema::hasColumn('don_hang', 'du_lieu_dia_chi_giao_hang')) {
            Schema::table('don_hang', function (Blueprint $table) {
                $table->json('du_lieu_dia_chi_giao_hang')
                    ->nullable()
                    ->after('ma_dia_chi_giao_hang');
            });
        }

        $donHangs = DB::table('don_hang')
            ->join(
                'dia_chi_giao_hang',
                'dia_chi_giao_hang.ma_dia_chi_giao_hang',
                '=',
                'don_hang.ma_dia_chi_giao_hang'
            )
            ->select(
                'don_hang.ma_don_hang',
                'dia_chi_giao_hang.ho_ten',
                'dia_chi_giao_hang.so_dien_thoai',
                'dia_chi_giao_hang.dia_chi',
                'dia_chi_giao_hang.tinh_thanh',
                'dia_chi_giao_hang.ma_tinh',
                'dia_chi_giao_hang.ma_huyen',
                'dia_chi_giao_hang.ma_xa'
            )
            ->get();

        foreach ($donHangs as $donHang) {
            DB::table('don_hang')
                ->where('ma_don_hang', $donHang->ma_don_hang)
                ->update([
                    'du_lieu_dia_chi_giao_hang' => json_encode([
                        'ho_ten' => $donHang->ho_ten,
                        'so_dien_thoai' => $donHang->so_dien_thoai,
                        'dia_chi' => $donHang->dia_chi,
                        'tinh_thanh' => $donHang->tinh_thanh,
                        'ma_tinh' => $donHang->ma_tinh,
                        'ma_huyen' => $donHang->ma_huyen,
                        'ma_xa' => $donHang->ma_xa,
                    ], JSON_UNESCAPED_UNICODE),
                ]);
        }
    }

    // Xoa khoa ngoai hien tai ma khong phu thuoc vao ten cu.
    private function xoaKhoaNgoaiDiaChi()
    {
        $tenKhoaNgoais = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'don_hang')
            ->where('COLUMN_NAME', 'ma_dia_chi_giao_hang')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->pluck('CONSTRAINT_NAME');

        foreach ($tenKhoaNgoais as $tenKhoaNgoai) {
            $tenAnToan = str_replace('`', '``', $tenKhoaNgoai);
            DB::statement(
                'ALTER TABLE `don_hang` DROP FOREIGN KEY `'.$tenAnToan.'`'
            );
        }
    }

    // Tao mot dia chi rieng cho moi don de lich su khong bi thay doi.
    private function chuyenDiaChiCuSangBangDiaChi()
    {
        $donHangs = DB::table('don_hang')
            ->select(
                'ma_don_hang',
                'ma_dia_chi_giao_hang',
                'du_lieu_dia_chi_giao_hang',
                'created_at',
                'updated_at'
            )
            ->orderBy('ma_don_hang')
            ->get();

        foreach ($donHangs as $donHang) {
            $duLieu = json_decode(
                (string) $donHang->du_lieu_dia_chi_giao_hang,
                true
            );

            if (! is_array($duLieu)) {
                $duLieu = [];
            }

            $diaChiHienTai = null;
            if ($donHang->ma_dia_chi_giao_hang) {
                $diaChiHienTai = DB::table('dia_chi_giao_hang')
                    ->where(
                        'ma_dia_chi_giao_hang',
                        $donHang->ma_dia_chi_giao_hang
                    )
                    ->first();
            }

            $diaChiMoi = $this->layDuLieuDiaChi(
                $duLieu,
                $diaChiHienTai,
                $donHang
            );

            if ($this->laBanSaoDiaChiDonHang($diaChiHienTai, $diaChiMoi)) {
                continue;
            }

            $maDiaChiMoi = DB::table('dia_chi_giao_hang')
                ->insertGetId($diaChiMoi);

            DB::table('don_hang')
                ->where('ma_don_hang', $donHang->ma_don_hang)
                ->update(['ma_dia_chi_giao_hang' => $maDiaChiMoi]);
        }
    }

    // Lay du lieu JSON, neu thieu thi lay tu dia chi dang duoc tham chieu.
    private function layDuLieuDiaChi($duLieu, $diaChiHienTai, $donHang)
    {
        $layGiaTri = function ($tenCot, $macDinh = null) use (
            $duLieu,
            $diaChiHienTai
        ) {
            if (array_key_exists($tenCot, $duLieu)) {
                return $duLieu[$tenCot];
            }

            if ($diaChiHienTai && isset($diaChiHienTai->{$tenCot})) {
                return $diaChiHienTai->{$tenCot};
            }

            return $macDinh;
        };

        $hoTen = trim((string) $layGiaTri('ho_ten'));
        $soDienThoai = trim((string) $layGiaTri('so_dien_thoai'));
        $diaChi = trim((string) $layGiaTri('dia_chi'));
        $tinhThanh = trim((string) $layGiaTri('tinh_thanh'));

        if ($hoTen == '' || $soDienThoai == '' || $diaChi == '' || $tinhThanh == '') {
            throw new RuntimeException(
                'Dia chi cua don hang '.$donHang->ma_don_hang.' khong day du.'
            );
        }

        return [
            'ma_nguoi_dung' => null,
            'ho_ten' => $hoTen,
            'so_dien_thoai' => $soDienThoai,
            'dia_chi' => $diaChi,
            'tinh_thanh' => $tinhThanh,
            'ma_tinh' => $layGiaTri('ma_tinh'),
            'ma_huyen' => $layGiaTri('ma_huyen'),
            'ma_xa' => $layGiaTri('ma_xa'),
            'mac_dinh' => false,
            'created_at' => $donHang->created_at,
            'updated_at' => $donHang->updated_at,
        ];
    }

    // Cho phep chay lai an toan neu lan truoc dung sau buoc sao chep.
    private function laBanSaoDiaChiDonHang($diaChiHienTai, $diaChiMoi)
    {
        if (! $diaChiHienTai || $diaChiHienTai->ma_nguoi_dung !== null) {
            return false;
        }

        foreach ([
            'ho_ten',
            'so_dien_thoai',
            'dia_chi',
            'tinh_thanh',
            'ma_tinh',
            'ma_huyen',
            'ma_xa',
        ] as $tenCot) {
            if ((string) $diaChiHienTai->{$tenCot} !== (string) $diaChiMoi[$tenCot]) {
                return false;
            }
        }

        return true;
    }
};
