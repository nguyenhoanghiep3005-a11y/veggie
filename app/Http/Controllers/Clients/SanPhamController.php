<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SanPhamController extends Controller
{
    // Hien thi danh sach san pham co phan trang.
    public function hienThiDanhSachSanPham(Request $request)
    {
        $danhMucs = DanhMuc::with('sanPhams')->get();
        $maDanhMucDaChon = (int) $request->input('ma_danh_muc', 0);
        $query = $this->taoTruyVanSanPham();

        if ($maDanhMucDaChon > 0) {
            $query->where('ma_danh_muc', $maDanhMucDaChon);
        }

        $sanPhams = $query
            ->orderBy('ma_san_pham', 'desc')
            ->paginate(9)
            ->appends($request->only('ma_danh_muc'));

        $this->chuanBiDanhSachSanPham($sanPhams);

        return view(
            'clients.pages.san-pham',
            compact('danhMucs', 'sanPhams', 'maDanhMucDaChon')
        );
    }

    // Loc san pham theo danh muc, khoang gia hien tai va cach sap xep.
    public function locSanPham(Request $request)
    {
        $data = $request->validate([
            'ma_danh_muc' => 'nullable|integer',
            'gia_tu' => 'nullable|numeric|min:0',
            'gia_den' => 'nullable|numeric|min:0',
            'sap_xep' => 'nullable|in:mac_dinh,moi_nhat,gia_tang,gia_giam',
        ]);

        $query = $this->taoTruyVanSanPham();

        if (isset($data['ma_danh_muc']) && (int) $data['ma_danh_muc'] > 0) {
            $query->where('ma_danh_muc', $data['ma_danh_muc']);
        }

        $danhSachSanPham = $query->get()->all();
        $danhSachSanPham = $this->locSanPhamTheoGiaHienTai($danhSachSanPham, $data);

        $sapXep = 'mac_dinh';
        if (isset($data['sap_xep'])) {
            $sapXep = $data['sap_xep'];
        }
        $danhSachSanPham = $this->sapXepDanhSachSanPham($danhSachSanPham, $sapXep);

        $sanPhams = $this->taoPhanTrangSanPham($danhSachSanPham, $request);
        $this->chuanBiDanhSachSanPham($sanPhams);

        return response()->json([
            'noi_dung' => view(
                'clients.components.luoi-san-pham',
                compact('sanPhams')
            )->render(),
            'phan_trang' => $sanPhams
                ->links('clients.components.pagination.phan-trang')
                ->toHtml(),
        ]);
    }

    // Loc san pham bang gia dang ban, neu co khuyen mai thi lay gia khuyen mai.
    private function locSanPhamTheoGiaHienTai($danhSachSanPham, $data)
    {
        if (! isset($data['gia_tu']) || ! isset($data['gia_den'])) {
            return $danhSachSanPham;
        }

        $giaTu = (float) $data['gia_tu'];
        $giaDen = (float) $data['gia_den'];
        $sanPhamsDaLoc = [];

        foreach ($danhSachSanPham as $sanPham) {
            $giaHienTai = (float) $sanPham->gia_hien_tai;

            if ($giaHienTai >= $giaTu && $giaHienTai <= $giaDen) {
                $sanPhamsDaLoc[] = $sanPham;
            }
        }

        return $sanPhamsDaLoc;
    }

    // Sap xep danh sach san pham theo lua chon cua khach hang.
    private function sapXepDanhSachSanPham($danhSachSanPham, $sapXep)
    {
        $tongSanPham = count($danhSachSanPham);

        for ($i = 0; $i < $tongSanPham - 1; $i++) {
            for ($j = $i + 1; $j < $tongSanPham; $j++) {
                if ($this->canDoiChoSanPham($danhSachSanPham[$i], $danhSachSanPham[$j], $sapXep)) {
                    $sanPhamTam = $danhSachSanPham[$i];
                    $danhSachSanPham[$i] = $danhSachSanPham[$j];
                    $danhSachSanPham[$j] = $sanPhamTam;
                }
            }
        }

        return $danhSachSanPham;
    }

    // Kiem tra hai san pham co can doi cho khi sap xep hay khong.
    private function canDoiChoSanPham($sanPhamTruoc, $sanPhamSau, $sapXep)
    {
        if ($sapXep == 'gia_tang') {
            return (float) $sanPhamTruoc->gia_hien_tai > (float) $sanPhamSau->gia_hien_tai;
        }

        if ($sapXep == 'gia_giam') {
            return (float) $sanPhamTruoc->gia_hien_tai < (float) $sanPhamSau->gia_hien_tai;
        }

        if ($sapXep == 'moi_nhat') {
            return $sanPhamTruoc->created_at < $sanPhamSau->created_at;
        }

        return (int) $sanPhamTruoc->ma_san_pham < (int) $sanPhamSau->ma_san_pham;
    }

    // Tao phan trang cho danh sach san pham da loc bang PHP.
    private function taoPhanTrangSanPham($danhSachSanPham, Request $request)
    {
        $soSanPhamMoiTrang = 9;
        $trangHienTai = (int) $request->input('page', 1);

        if ($trangHienTai < 1) {
            $trangHienTai = 1;
        }

        $viTriBatDau = ($trangHienTai - 1) * $soSanPhamMoiTrang;
        $sanPhamsTrongTrang = array_slice($danhSachSanPham, $viTriBatDau, $soSanPhamMoiTrang);

        $sanPhams = new LengthAwarePaginator(
            $sanPhamsTrongTrang,
            count($danhSachSanPham),
            $soSanPhamMoiTrang,
            $trangHienTai,
            ['path' => url('/san-pham/loc')]
        );

        $sanPhams->appends($request->only('ma_danh_muc', 'gia_tu', 'gia_den', 'sap_xep'));

        return $sanPhams;
    }

    // Hien thi chi tiet, bien the va san pham lien quan.
    public function hienThiChiTietSanPham($slug)
    {
        $sanPham = $this->timSanPhamChiTiet($slug);
        $this->chuanBiSanPhamHienThi($sanPham);

        $bienTheSanPhams = $this->layDanhSachBienTheSanPham($sanPham);
        $sanPhamLienQuans = $this->taoTruyVanSanPham()
            ->where('ma_danh_muc', $sanPham->ma_danh_muc)
            ->where('ma_san_pham', '!=', $sanPham->ma_san_pham)
            ->orderBy('ma_san_pham', 'desc')
            ->paginate(6, ['*'], 'lien_quan');

        $this->chuanBiDanhSachSanPham($sanPhamLienQuans);

        return view(
            'clients.pages.chi-tiet-san-pham',
            compact('sanPham', 'bienTheSanPhams', 'sanPhamLienQuans')
        );
    }

    // Tra thong tin bien the de JavaScript cap nhat trang chi tiet.
    public function layThongTinBienTheSanPham($slug)
    {
        $sanPham = $this->timSanPhamChiTiet($slug);
        $this->chuanBiSanPhamHienThi($sanPham);
        $bienTheSanPhams = $this->layDanhSachBienTheSanPham($sanPham);
        $cacBienThe = [];

        foreach ($bienTheSanPhams as $bienTheSanPham) {
            $this->chuanBiSanPhamHienThi($bienTheSanPham);
            $tonKho = $bienTheSanPham->soLuongCoTheBan();
            $tenTonKho = 'Hết hàng';
            if ($tonKho > 0) {
                $tenTonKho = 'Còn '.$tonKho;
            }

            $cacBienThe[] = [
                'ma_san_pham' => $bienTheSanPham->ma_san_pham,
                'duong_dan' => $bienTheSanPham->duong_dan,
                'duong_dan_chi_tiet' => route(
                    'san-pham.chi-tiet',
                    $bienTheSanPham->duong_dan
                ),
                'duong_dan_lay_bien_the' => route(
                    'san-pham.bien-the',
                    $bienTheSanPham->duong_dan
                ),
                'ten_bien_the' => $bienTheSanPham->ten_bien_the,
                'ton_kho' => $tonKho,
                'ten_ton_kho' => $bienTheSanPham->tenSoLuongCoTheBan(),
                'dang_duoc_chon' => $bienTheSanPham->ma_san_pham
                    == $sanPham->ma_san_pham,
            ];
        }

        $tenDanhMuc = '';
        if ($sanPham->danhMuc) {
            $tenDanhMuc = $sanPham->danhMuc->ten;
        }

        return response()->json([
            'trang_thai' => true,
            'du_lieu' => [
                'ma_san_pham' => $sanPham->ma_san_pham,
                'duong_dan' => $sanPham->duong_dan,
                'duong_dan_chi_tiet' => route(
                    'san-pham.chi-tiet',
                    $sanPham->duong_dan
                ),
                'ten' => $sanPham->ten_goc,
                'ten_hien_thi' => $sanPham->ten_hien_thi,
                'gia' => number_format($sanPham->gia_hien_tai, 0, ',', '.'),
                'gia_goc' => number_format($sanPham->gia, 0, ',', '.'),
                'dang_khuyen_mai' => $sanPham->gia_hien_tai < $sanPham->gia,
                'so_sao_trung_binh' => $sanPham->so_sao_trung_binh,
                'tong_danh_gia' => $sanPham->tong_danh_gia,
                'so_luong_da_ban' => $sanPham->so_luong_da_ban,
                'ton_kho' => $sanPham->soLuongCoTheBan(),
                'ten_ton_kho' => $sanPham->tenSoLuongCoTheBan(),
                'co_the_mua' => $sanPham->soLuongCoTheBan() > 0,
                'hinh_anh' => $sanPham->duong_dan_hinh_anh,
                'cac_hinh_anh' => $sanPham->cac_hinh_anh_chi_tiet,
                'ten_bien_the' => $sanPham->ten_bien_the,
                'mo_ta' => $sanPham->mo_ta_hien_thi,
                'san_xuat' => $sanPham->san_xuat_hien_thi,
                'thuong_hieu' => $sanPham->thuong_hieu_hien_thi,
                'bao_quan' => $sanPham->bao_quan_hien_thi,
                'cach_dung' => $sanPham->cach_dung_hien_thi,
                'thanh_phan' => $sanPham->thanh_phan_hien_thi,
                'ten_danh_muc' => $tenDanhMuc,
                'cac_bien_the' => $cacBienThe,
            ],
        ]);
    }

    // Tao truy van chung cho san pham dang ban.
    private function taoTruyVanSanPham()
    {
        return SanPham::with([
            'hinhAnhDauTien',
            'hinhAnhs',
            'danhGias',
            'chiTietDonHangs.donHang',
        ])->whereHas('loHangKhos', function ($query) {
            $query->where('so_luong_con', '>', 0)
                ->whereDate('han_su_dung', '>=', today());
        });
    }

    // Tim san pham chi tiet theo duong dan.
    private function timSanPhamChiTiet($slug)
    {
        $sanPham = SanPham::with([
            'danhMuc',
            'hinhAnhs',
            'hinhAnhDauTien',
            'danhGias.nguoiDung',
            'chiTietDonHangs.donHang',
        ])->where('duong_dan', $slug)
            ->whereHas('loHangKhos', function ($query) {
                $query->where('so_luong_con', '>', 0)
                    ->whereDate('han_su_dung', '>=', today());
            })
            ->firstOrFail();

        $thongTinMoTa = $this->tachThongTinMoTa($sanPham->mo_ta);
        $sanPham->mo_ta_hien_thi = $thongTinMoTa['mo_ta'];
        $sanPham->san_xuat_hien_thi = $thongTinMoTa['san_xuat'];
        $sanPham->thuong_hieu_hien_thi = $thongTinMoTa['thuong_hieu'];
        $sanPham->bao_quan_hien_thi = $thongTinMoTa['bao_quan'];
        $sanPham->cach_dung_hien_thi = $thongTinMoTa['cach_dung'];
        $sanPham->thanh_phan_hien_thi = $thongTinMoTa['thanh_phan'];
        $sanPham->nguon_goc_hien_thi = $thongTinMoTa['san_xuat'];
        $sanPham->cac_hinh_anh_chi_tiet = $this->layHinhAnhSanPham($sanPham);

        return $sanPham;
    }

    // Lay cac san pham cung ten goc de hien thi lua chon bien the.
    private function layDanhSachBienTheSanPham($sanPham)
    {
        $cacSanPhamCungDanhMuc = SanPham::with([
            'hinhAnhDauTien',
            'hinhAnhs',
            'danhGias',
            'chiTietDonHangs.donHang',
        ])->where('ma_danh_muc', $sanPham->ma_danh_muc)
            ->whereHas('loHangKhos', function ($query) {
                $query->where('so_luong_con', '>', 0)
                    ->whereDate('han_su_dung', '>=', today());
            })
            ->orderBy('ma_san_pham', 'asc')
            ->get();

        $bienTheSanPhams = [];

        foreach ($cacSanPhamCungDanhMuc as $sanPhamCungDanhMuc) {
            if (
                mb_strtolower($sanPhamCungDanhMuc->ten_goc, 'UTF-8')
                == mb_strtolower($sanPham->ten_goc, 'UTF-8')
            ) {
                $sanPhamCungDanhMuc->dang_duoc_chon =
                    $sanPhamCungDanhMuc->ma_san_pham
                    == $sanPham->ma_san_pham;
                $bienTheSanPhams[] = $sanPhamCungDanhMuc;
            }
        }

        return $bienTheSanPhams;
    }

    // Chuan bi so sao, danh gia va so luong da ban cho mot san pham.
    private function chuanBiSanPhamHienThi($sanPham)
    {
        $tongSoSao = 0;

        foreach ($sanPham->danhGias as $danhGia) {
            $tongSoSao += $danhGia->so_sao;
        }

        $sanPham->tong_danh_gia = $sanPham->danhGias->count();
        $sanPham->so_sao_trung_binh = 0;

        if ($sanPham->tong_danh_gia > 0) {
            $sanPham->so_sao_trung_binh = round(
                $tongSoSao / $sanPham->tong_danh_gia,
                1
            );
        }

        $sanPham->so_luong_da_ban = $sanPham->soLuongDaBan();
    }

    // Chuan bi thong tin hien thi cho danh sach san pham.
    private function chuanBiDanhSachSanPham($sanPhams)
    {
        foreach ($sanPhams as $sanPham) {
            $this->chuanBiSanPhamHienThi($sanPham);
        }
    }

    // Lay danh sach anh chi tiet, neu khong co thi dung anh mac dinh.
    private function layHinhAnhSanPham($sanPham)
    {
        $duongDanHinhAnhs = [];

        foreach ($sanPham->hinhAnhs as $hinhAnh) {
            $duongDanHinhAnhs[] = $hinhAnh->duong_dan_hinh_anh;
        }

        if (count($duongDanHinhAnhs) == 0) {
            $duongDanHinhAnhs[] = asset(
                'storage/uploads/products/default.png'
            );
        }

        return $duongDanHinhAnhs;
    }

    // Tach mo ta san pham de hien thi tung dong thong tin ro rang.
    private function tachThongTinMoTa($moTa)
    {
        $macDinh = '';
        $thongTin = [
            'mo_ta' => $macDinh,
            'san_xuat' => $macDinh,
            'thuong_hieu' => $macDinh,
            'bao_quan' => $macDinh,
            'cach_dung' => $macDinh,
            'thanh_phan' => $macDinh,
        ];

        $cacDong = preg_split("/\r\n|\n|\r/", (string) $moTa);
        $dongHopLes = [];

        foreach ($cacDong as $dong) {
            $dong = trim($dong);

            if ($dong != '') {
                $dongHopLes[] = $dong;
            }
        }

        $thuTuDongKhongCoNhan = [
            'bao_quan',
            'thuong_hieu',
            'san_xuat',
            'cach_dung',
            'thanh_phan',
        ];
        $viTriDongKhongCoNhan = 0;

        for ($viTri = 0; $viTri < count($dongHopLes); $viTri++) {
            $dong = $dongHopLes[$viTri];
            $loaiThongTin = $this->xacDinhLoaiDongMoTa($dong);
            $coNhanThongTin = $loaiThongTin != '';

            if ($viTri == 0) {
                if ($loaiThongTin == 'mo_ta') {
                    $thongTin['mo_ta'] = $this->xoaNhanMoTa($dong);
                } else {
                    $thongTin['mo_ta'] = $dong;
                }

                continue;
            }

            if ($loaiThongTin == '') {
                if (isset($thuTuDongKhongCoNhan[$viTriDongKhongCoNhan])) {
                    $loaiThongTin = $thuTuDongKhongCoNhan[$viTriDongKhongCoNhan];
                    $viTriDongKhongCoNhan++;
                }
            }

            if ($loaiThongTin != '' && isset($thongTin[$loaiThongTin])) {
                if ($coNhanThongTin) {
                    $thongTin[$loaiThongTin] = $this->xoaNhanMoTa($dong);
                } else {
                    $thongTin[$loaiThongTin] = $dong;
                }
            }
        }

        return $thongTin;
    }

    // Xac dinh dong mo ta dang noi ve thong tin nao.
    private function xacDinhLoaiDongMoTa($noiDung)
    {
        $noiDungChuThuong = mb_strtolower(trim((string) $noiDung), 'UTF-8');

        if (mb_strpos($noiDungChuThuong, 'mô tả') === 0 || mb_strpos($noiDungChuThuong, 'mo ta') === 0) {
            return 'mo_ta';
        }

        if (mb_strpos($noiDungChuThuong, 'bảo quản') === 0 || mb_strpos($noiDungChuThuong, 'bao quan') === 0) {
            return 'bao_quan';
        }

        if (mb_strpos($noiDungChuThuong, 'thương hiệu') === 0 || mb_strpos($noiDungChuThuong, 'thuong hieu') === 0) {
            return 'thuong_hieu';
        }

        if (mb_strpos($noiDungChuThuong, 'cách dùng') === 0 || mb_strpos($noiDungChuThuong, 'cach dung') === 0) {
            return 'cach_dung';
        }

        if (mb_strpos($noiDungChuThuong, 'thành phần') === 0 || mb_strpos($noiDungChuThuong, 'thanh phan') === 0) {
            return 'thanh_phan';
        }

        if (mb_strpos($noiDungChuThuong, 'nơi sản xuất') === 0 || mb_strpos($noiDungChuThuong, 'noi san xuat') === 0) {
            return 'san_xuat';
        }

        if (mb_strpos($noiDungChuThuong, 'sản xuất') === 0 || mb_strpos($noiDungChuThuong, 'san xuat') === 0) {
            return 'san_xuat';
        }

        if (mb_strpos($noiDungChuThuong, 'xuất xứ') === 0 || mb_strpos($noiDungChuThuong, 'xuat xu') === 0) {
            return 'san_xuat';
        }

        if (mb_strpos($noiDungChuThuong, 'nguồn gốc') === 0 || mb_strpos($noiDungChuThuong, 'nguon goc') === 0) {
            return 'san_xuat';
        }

        return '';
    }

    // Xoa phan nhan truoc dau hai cham, vi du Bao quan: ...
    private function xoaNhanMoTa($noiDung)
    {
        $viTriDauHaiCham = mb_strpos($noiDung, ':', 0, 'UTF-8');

        if ($viTriDauHaiCham !== false) {
            return trim(mb_substr($noiDung, $viTriDauHaiCham + 1, null, 'UTF-8'));
        }

        return trim($noiDung);
    }
}