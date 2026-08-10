<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;

    public const SO_NGAY_CAN_HAN = 30;

    protected $table = 'san_pham';

    protected $primaryKey = 'ma_san_pham';

    protected $fillable = [
        'ten',
        'duong_dan',
        'ma_danh_muc',
        'mo_ta',
        'gia',
        'don_vi',
        'danh_gia_trung_binh',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'danh_gia_trung_binh' => 'decimal:2',
    ];

    // Lấy danh mục của sản phẩm.
    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'ma_danh_muc');
    }

    // Lấy các hình ảnh của sản phẩm.
    public function hinhAnhs()
    {
        return $this->hasMany(HinhAnhSanPham::class, 'ma_san_pham')
            ->orderBy('ma_hinh_anh_san_pham');
    }

    // Lấy hình ảnh đầu tiên của sản phẩm.
    public function hinhAnhDauTien()
    {
        return $this->hasOne(HinhAnhSanPham::class, 'ma_san_pham')
            ->orderBy('ma_hinh_anh_san_pham');
    }

    // Lấy các đánh giá của sản phẩm.
    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'ma_san_pham');
    }

    // Lấy các dòng đơn hàng có sản phẩm.
    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'ma_san_pham');
    }

    // Lấy các dòng đặt nhập của sản phẩm.
    public function chiTietDonDatNhaps()
    {
        return $this->hasMany(ChiTietDonDatNhap::class, 'ma_san_pham');
    }

    // Lấy các dòng phiếu nhập của sản phẩm.
    public function chiTietPhieuNhaps()
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'ma_san_pham');
    }

    // Lấy các lô hàng trong kho của sản phẩm.
    public function loHangKhos()
    {
        return $this->hasMany(LoHangKho::class, 'ma_san_pham');
    }

    // Lấy các dòng hàng hư của sản phẩm.
    public function chiTietPhieuHangHus()
    {
        return $this->hasMany(ChiTietPhieuHangHu::class, 'ma_san_pham');
    }

    // Tạo truy vấn lấy các lô còn hàng và chưa hết hạn.
    public function layCacLoHangCoTheBan()
    {
        return $this->loHangKhos()
            ->where('so_luong_con', '>', 0)
            ->whereDate('han_su_dung', '>=', today())
            ->orderBy('han_su_dung')
            ->orderBy('ma_lo_hang_kho');
    }

    // Tinh so luong khach co the mua theo gia dang hien thi.
    public function soLuongCoTheBan()
    {
        if ($this->layGiaKhuyenMaiDangCo() > 0) {
            return $this->soLuongKhuyenMaiCoTheBan();
        }

        return $this->soLuongThuongCoTheBan();
    }

    // Tinh so luong con ban duoc cua cac lo dang khuyen mai.
    public function soLuongKhuyenMaiCoTheBan()
    {
        $giaKhuyenMai = (float) $this->layGiaKhuyenMaiDangCo();

        if ($giaKhuyenMai <= 0) {
            return 0;
        }

        return (int) $this->layCacLoHangCoTheBan()
            ->where('gia_khuyen_mai', $giaKhuyenMai)
            ->sum('so_luong_con');
    }

    // Tinh so luong con ban duoc cua cac lo khong khuyen mai.
    public function soLuongThuongCoTheBan()
    {
        $soLuong = 0;
        $loHangs = $this->layCacLoHangCoTheBan()->get();

        foreach ($loHangs as $loHang) {
            $giaKhuyenMai = (float) $loHang->gia_khuyen_mai;

            if ($giaKhuyenMai <= 0 || $giaKhuyenMai >= (float) $this->gia) {
                $soLuong += (int) $loHang->so_luong_con;
            }
        }

        return $soLuong;
    }

    // Lay dong chu hien thi so luong ma khach co the mua.
    public function tenSoLuongCoTheBan()
    {
        $soLuong = $this->soLuongCoTheBan();

        if ($soLuong <= 0) {
            return 'Hết hàng';
        }

        return 'Còn '.$soLuong;
    }

    // Tính tổng số lượng sản phẩm đã bán thành công.
    public function soLuongDaBan()
    {
        $soLuongDaBan = 0;
        $chiTietDonHangs = $this->chiTietDonHangs()->with('donHang')->get();

        foreach ($chiTietDonHangs as $chiTietDonHang) {
            if ($chiTietDonHang->donHang && $chiTietDonHang->donHang->trang_thai == 'hoan_thanh') {
                $soLuongDaBan += (int) $chiTietDonHang->so_luong;
            }
        }

        return $soLuongDaBan;
    }

    // Lấy giá bán của một lô hàng.
    public function layGiaTheoLo($loHang)
    {
        $giaKhuyenMai = (float) $loHang->gia_khuyen_mai;

        if ($giaKhuyenMai > 0 && $giaKhuyenMai < (float) $this->gia) {
            return $giaKhuyenMai;
        }

        return (float) $this->gia;
    }

    // Lấy giá khuyến mãi thấp nhất trong các lô còn bán được.
    public function layGiaKhuyenMaiDangCo()
    {
        $giaKhuyenMaiDangCo = 0;
        $loHangs = $this->layCacLoHangCoTheBan()->get();

        foreach ($loHangs as $loHang) {
            $giaKhuyenMai = (float) $loHang->gia_khuyen_mai;

            if ($giaKhuyenMai <= 0 || $giaKhuyenMai >= (float) $this->gia) {
                continue;
            }

            if ($giaKhuyenMaiDangCo == 0 || $giaKhuyenMai < $giaKhuyenMaiDangCo) {
                $giaKhuyenMaiDangCo = $giaKhuyenMai;
            }
        }

        return $giaKhuyenMaiDangCo;
    }

    // Lấy giá đang bán, nếu có lô khuyến mãi thì ưu tiên giá khuyến mãi.
    public function getGiaHienTaiAttribute()
    {
        $giaKhuyenMai = $this->layGiaKhuyenMaiDangCo();

        if ($giaKhuyenMai > 0) {
            return $giaKhuyenMai;
        }

        return (float) $this->gia;
    }

    // Kiểm tra sản phẩm đang có giá khuyến mãi.
    public function getDangKhuyenMaiAttribute()
    {
        return (float) $this->gia_hien_tai < (float) $this->gia;
    }

    // Lấy tên sản phẩm không gồm nhãn khối lượng của biến thể.
    public function getTenGocAttribute()
    {
        $tenSanPham = trim((string) $this->ten);
        $tenBienThe = trim((string) $this->ten_bien_the);

        if ($tenBienThe != '' && $tenBienThe != 'Mặc định') {
            $tenSanPham = preg_replace('/\s*' . preg_quote($tenBienThe, '/') . '$/iu', '', $tenSanPham);
        }

        $tenSanPham = preg_replace('/\s+\d+(?:[,.]\d+)?\s*(g|gram|kg)$/iu', '', $tenSanPham);
        $tenSanPham = trim($tenSanPham);

        if ($tenSanPham != '') {
            return $tenSanPham;
        }

        return trim((string) $this->ten);
    }

    // Lấy nhãn khối lượng của biến thể sản phẩm.
    public function getTenBienTheAttribute()
    {
        if (preg_match('/(\d+(?:[,.]\d+)?\s*(g|gram|kg))$/iu', (string) $this->ten, $ketQua)) {
            $tenBienThe = str_replace(' ', '', $ketQua[1]);

            return preg_replace('/gram$/i', 'g', $tenBienThe);
        }

        return 'Mặc định';
    }

    // Ghép tên gốc và nhãn biến thể để hiển thị.
    public function getTenHienThiAttribute()
    {
        if ($this->ten_bien_the == '' || $this->ten_bien_the == 'Mặc định') {
            return $this->ten_goc;
        }

        return trim($this->ten_goc . ' ' . $this->ten_bien_the);
    }

    // Lấy đường dẫn hình ảnh chính của sản phẩm.
    public function getDuongDanHinhAnhAttribute()
    {
        if ($this->hinhAnhDauTien && $this->hinhAnhDauTien->hinh_anh) {
            return asset('storage/' . $this->hinhAnhDauTien->hinh_anh);
        }

        $hinhAnhThayThe = $this->layHinhAnhBienTheThayThe();
        if ($hinhAnhThayThe) {
            return asset('storage/' . $hinhAnhThayThe->hinh_anh);
        }

        return asset('storage/uploads/products/default.png');
    }

    // Tru ton kho khi khach dat hang.
    public function truTonKho($soLuong)
    {
        $soLuongCon = (int) $soLuong;
        $phanBoTonKhos = [];
        $giaDangBan = (float) $this->gia_hien_tai;
        $loHangKhos = $this->layCacLoHangCoTheBan()->get();

        if ($soLuongCon <= 0) {
            return [];
        }

        if ($this->soLuongCoTheBan() < $soLuongCon) {
            throw new Exception('Sản phẩm "' . $this->ten_hien_thi . '" không đủ hàng.');
        }

        foreach ($loHangKhos as $loHangKho) {
            if ($soLuongCon <= 0) {
                break;
            }

            $giaLoHang = $this->layGiaTheoLo($loHangKho);
            if ($giaLoHang != $giaDangBan) {
                continue;
            }

            $soLuongLay = $soLuongCon;
            if ((int) $loHangKho->so_luong_con < $soLuongLay) {
                $soLuongLay = (int) $loHangKho->so_luong_con;
            }

            $loHangKho->so_luong_con -= $soLuongLay;
            $loHangKho->save();

            $phanBoTonKhos[] = [
                'ma_lo_hang_kho' => $loHangKho->ma_lo_hang_kho,
                'so_luong' => $soLuongLay,
                'gia' => $giaLoHang,
            ];

            $soLuongCon -= $soLuongLay;
        }

        if ($soLuongCon > 0) {
            throw new Exception('Sản phẩm "' . $this->ten_hien_thi . '" không đủ hàng.');
        }

        return $phanBoTonKhos;
    }

    // Hoan lai ton kho khi don hang bi huy hoac hang hoan con nguyen ven.
    public function hoanTonKho($soLuong, $phanBoTonKhos = [])
    {
        $soLuong = (int) $soLuong;
        if ($soLuong <= 0) {
            return;
        }

        foreach ($phanBoTonKhos as $phanBoTonKho) {
            $maLoHangKho = null;
            $soLuongHoan = 0;

            if (isset($phanBoTonKho['ma_lo_hang_kho'])) {
                $maLoHangKho = $phanBoTonKho['ma_lo_hang_kho'];
            }

            if (isset($phanBoTonKho['so_luong'])) {
                $soLuongHoan = (int) $phanBoTonKho['so_luong'];
            }

            if (! $maLoHangKho || $soLuongHoan <= 0) {
                continue;
            }

            $loHangKho = LoHangKho::find($maLoHangKho);

            if ($loHangKho && (int) $loHangKho->ma_san_pham == (int) $this->ma_san_pham) {
                $loHangKho->so_luong_con += $soLuongHoan;
                $loHangKho->save();
            }
        }
    }
    // Tìm hình ảnh từ biến thể cùng tên khi sản phẩm chưa có ảnh.
    private function layHinhAnhBienTheThayThe()
    {
        if (! $this->ma_danh_muc) {
            return null;
        }

        $tenGoc = mb_strtolower($this->ten_goc, 'UTF-8');
        $sanPhams = self::with('hinhAnhDauTien')
            ->where('ma_danh_muc', $this->ma_danh_muc)
            ->where('ma_san_pham', '!=', $this->ma_san_pham)
            ->get();

        foreach ($sanPhams as $sanPham) {
            if (
                $sanPham->hinhAnhDauTien
                && mb_strtolower($sanPham->ten_goc, 'UTF-8') == $tenGoc
            ) {
                return $sanPham->hinhAnhDauTien;
            }
        }

        return null;
    }
}
