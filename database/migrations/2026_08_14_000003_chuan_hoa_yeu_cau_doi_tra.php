<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('chi_tiet_yeu_cau_doi_tra')) {
            Schema::create('chi_tiet_yeu_cau_doi_tra', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id('ma_chi_tiet_yeu_cau_doi_tra');
                $table->foreignId('ma_yeu_cau_doi_tra')
                    ->constrained('yeu_cau_doi_tra', 'ma_yeu_cau_doi_tra')
                    ->cascadeOnDelete();
                $table->foreignId('ma_chi_tiet_don_hang')
                    ->constrained('chi_tiet_don_hang', 'ma_chi_tiet_don_hang')
                    ->cascadeOnDelete();
                $table->unsignedInteger('so_luong');
                $table->boolean('da_xuat_hang_doi')->default(false);
                $table->timestamps();

                $table->unique(
                    ['ma_yeu_cau_doi_tra', 'ma_chi_tiet_don_hang'],
                    'ct_ycdt_yeu_cau_chi_tiet_unique'
                );
            });
        }

        if (! Schema::hasTable('minh_chung_yeu_cau_doi_tra')) {
            Schema::create('minh_chung_yeu_cau_doi_tra', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id('ma_minh_chung_yeu_cau_doi_tra');
                $table->foreignId('ma_yeu_cau_doi_tra')
                    ->constrained('yeu_cau_doi_tra', 'ma_yeu_cau_doi_tra')
                    ->cascadeOnDelete();
                $table->string('o_dia')->default('public');
                $table->text('duong_dan');
                $table->string('ten_goc')->nullable();
                $table->string('loai_mime', 100)->nullable();
                $table->enum('loai_tep', ['hinh_anh', 'video'])
                    ->default('hinh_anh');
                $table->unsignedBigInteger('kich_thuoc')->default(0);
                $table->string('ma_cong_khai')->nullable();
                $table->timestamps();
            });
        }

        $this->chuyenSanPhamCu();
        $this->chuyenMinhChungCu();
        $this->kiemTraDuLieuDaChuyen();

        Schema::table('yeu_cau_doi_tra', function (Blueprint $table) {
            if (Schema::hasColumn('yeu_cau_doi_tra', 'san_pham')) {
                $table->dropColumn('san_pham');
            }
            if (Schema::hasColumn('yeu_cau_doi_tra', 'minh_chung')) {
                $table->dropColumn('minh_chung');
            }
        });
    }

    public function down()
    {
        Schema::table('yeu_cau_doi_tra', function (Blueprint $table) {
            if (! Schema::hasColumn('yeu_cau_doi_tra', 'san_pham')) {
                $table->json('san_pham')->nullable()->after('mo_ta');
            }
            if (! Schema::hasColumn('yeu_cau_doi_tra', 'minh_chung')) {
                $table->json('minh_chung')->nullable()->after('san_pham');
            }
        });

        $yeuCaus = DB::table('yeu_cau_doi_tra')->get();

        foreach ($yeuCaus as $yeuCau) {
            $sanPhams = DB::table('chi_tiet_yeu_cau_doi_tra')
                ->where('ma_yeu_cau_doi_tra', $yeuCau->ma_yeu_cau_doi_tra)
                ->orderBy('ma_chi_tiet_yeu_cau_doi_tra')
                ->get()
                ->map(function ($chiTiet) {
                    $duLieu = [
                        'ma_chi_tiet_don_hang' => (int) $chiTiet->ma_chi_tiet_don_hang,
                        'so_luong' => (int) $chiTiet->so_luong,
                    ];

                    if ($chiTiet->da_xuat_hang_doi) {
                        $duLieu['phan_bo_hang_doi'] = [[
                            'so_luong' => (int) $chiTiet->so_luong,
                        ]];
                    }

                    return $duLieu;
                })->all();

            $minhChungs = DB::table('minh_chung_yeu_cau_doi_tra')
                ->where('ma_yeu_cau_doi_tra', $yeuCau->ma_yeu_cau_doi_tra)
                ->orderBy('ma_minh_chung_yeu_cau_doi_tra')
                ->get()
                ->map(function ($minhChung) {
                    return [
                        'o_dia' => $minhChung->o_dia,
                        'duong_dan' => $minhChung->duong_dan,
                        'ten_goc' => $minhChung->ten_goc,
                        'loai_mime' => $minhChung->loai_mime,
                        'loai_tep' => $minhChung->loai_tep,
                        'kich_thuoc' => (int) $minhChung->kich_thuoc,
                        'ma_cong_khai' => $minhChung->ma_cong_khai,
                    ];
                })->all();

            DB::table('yeu_cau_doi_tra')
                ->where('ma_yeu_cau_doi_tra', $yeuCau->ma_yeu_cau_doi_tra)
                ->update([
                    'san_pham' => json_encode($sanPhams, JSON_UNESCAPED_UNICODE),
                    'minh_chung' => json_encode($minhChungs, JSON_UNESCAPED_UNICODE),
                ]);
        }

        Schema::dropIfExists('minh_chung_yeu_cau_doi_tra');
        Schema::dropIfExists('chi_tiet_yeu_cau_doi_tra');
    }

    private function chuyenSanPhamCu()
    {
        if (! Schema::hasColumn('yeu_cau_doi_tra', 'san_pham')) {
            return;
        }

        $yeuCaus = DB::table('yeu_cau_doi_tra')
            ->select('ma_yeu_cau_doi_tra', 'ma_don_hang', 'san_pham', 'created_at', 'updated_at')
            ->get();

        foreach ($yeuCaus as $yeuCau) {
            $sanPhams = json_decode($yeuCau->san_pham, true);

            if (! is_array($sanPhams) || count($sanPhams) === 0) {
                throw new \RuntimeException(
                    'Yeu cau doi tra '.$yeuCau->ma_yeu_cau_doi_tra.' khong co san pham hop le.'
                );
            }

            foreach ($sanPhams as $sanPham) {
                $maChiTiet = $sanPham['ma_chi_tiet_don_hang']
                    ?? $sanPham['order_item_id']
                    ?? null;
                $soLuong = (int) ($sanPham['so_luong']
                    ?? $sanPham['quantity']
                    ?? 0);
                $chiTietDonHang = DB::table('chi_tiet_don_hang')
                    ->where('ma_chi_tiet_don_hang', $maChiTiet)
                    ->where('ma_don_hang', $yeuCau->ma_don_hang)
                    ->first();

                if (! $chiTietDonHang || $soLuong <= 0 || $soLuong > $chiTietDonHang->so_luong) {
                    throw new \RuntimeException(
                        'San pham cua yeu cau doi tra '.$yeuCau->ma_yeu_cau_doi_tra.' khong hop le.'
                    );
                }

                $phanBoHangDoi = $sanPham['phan_bo_hang_doi']
                    ?? $sanPham['replacement_allocations']
                    ?? [];

                DB::table('chi_tiet_yeu_cau_doi_tra')->updateOrInsert(
                    [
                        'ma_yeu_cau_doi_tra' => $yeuCau->ma_yeu_cau_doi_tra,
                        'ma_chi_tiet_don_hang' => $maChiTiet,
                    ],
                    [
                        'so_luong' => $soLuong,
                        'da_xuat_hang_doi' => is_array($phanBoHangDoi)
                            && count($phanBoHangDoi) > 0,
                        'created_at' => $yeuCau->created_at,
                        'updated_at' => $yeuCau->updated_at,
                    ]
                );
            }
        }
    }

    private function chuyenMinhChungCu()
    {
        if (! Schema::hasColumn('yeu_cau_doi_tra', 'minh_chung')) {
            return;
        }

        $yeuCaus = DB::table('yeu_cau_doi_tra')
            ->select('ma_yeu_cau_doi_tra', 'minh_chung', 'created_at', 'updated_at')
            ->get();

        foreach ($yeuCaus as $yeuCau) {
            $minhChungs = json_decode($yeuCau->minh_chung, true);

            if ($minhChungs === null || $minhChungs === '') {
                $minhChungs = [];
            }

            if (! is_array($minhChungs)) {
                throw new \RuntimeException(
                    'Minh chung cua yeu cau doi tra '.$yeuCau->ma_yeu_cau_doi_tra.' khong hop le.'
                );
            }

            foreach ($minhChungs as $minhChung) {
                $duongDan = $minhChung['duong_dan']
                    ?? $minhChung['path']
                    ?? null;

                if (! $duongDan) {
                    throw new \RuntimeException(
                        'Minh chung cua yeu cau doi tra '.$yeuCau->ma_yeu_cau_doi_tra.' thieu duong dan.'
                    );
                }

                $loaiMime = $minhChung['loai_mime']
                    ?? $minhChung['mime_type']
                    ?? null;
                $loaiTepCu = $minhChung['loai_tep']
                    ?? $minhChung['media_type']
                    ?? null;
                $loaiTep = $loaiTepCu === 'video'
                    || ($loaiMime && str_starts_with($loaiMime, 'video/'))
                    ? 'video'
                    : 'hinh_anh';

                DB::table('minh_chung_yeu_cau_doi_tra')->updateOrInsert(
                    [
                        'ma_yeu_cau_doi_tra' => $yeuCau->ma_yeu_cau_doi_tra,
                        'duong_dan' => $duongDan,
                    ],
                    [
                        'o_dia' => $minhChung['o_dia']
                            ?? $minhChung['disk']
                            ?? 'public',
                        'ten_goc' => $minhChung['ten_goc']
                            ?? $minhChung['original_name']
                            ?? null,
                        'loai_mime' => $loaiMime,
                        'loai_tep' => $loaiTep,
                        'kich_thuoc' => (int) ($minhChung['kich_thuoc']
                            ?? $minhChung['size']
                            ?? 0),
                        'ma_cong_khai' => $minhChung['ma_cong_khai']
                            ?? $minhChung['public_id']
                            ?? null,
                        'created_at' => $yeuCau->created_at,
                        'updated_at' => $yeuCau->updated_at,
                    ]
                );
            }
        }
    }

    private function kiemTraDuLieuDaChuyen()
    {
        $thieuSanPham = DB::table('yeu_cau_doi_tra')
            ->leftJoin(
                'chi_tiet_yeu_cau_doi_tra',
                'chi_tiet_yeu_cau_doi_tra.ma_yeu_cau_doi_tra',
                '=',
                'yeu_cau_doi_tra.ma_yeu_cau_doi_tra'
            )
            ->whereNull('chi_tiet_yeu_cau_doi_tra.ma_chi_tiet_yeu_cau_doi_tra')
            ->exists();

        if ($thieuSanPham) {
            throw new \RuntimeException('Van con yeu cau doi tra chua co chi tiet san pham.');
        }
    }
};
