<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;

    public const SO_NGAY_CAN_HAN = 60;

    protected $table = 'san_pham';

    protected $primaryKey = 'ma_san_pham';

    protected $fillable = [
        'ten',
        'duong_dan',
        'ma_danh_muc',
        'mo_ta',
        'gia',
        'ton_kho',
        'trang_thai',
        'don_vi',
        'danh_gia_trung_binh',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'ton_kho' => 'integer',
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

    // Tính tổng số lượng sản phẩm đã bán thành công.
    public function soLuongDaBan()
    {
        $soLuongDaBan = 0;
        $chiTietDonHangs = $this->chiTietDonHangs()->with('donHang')->get();

        foreach ($chiTietDonHangs as $chiTietDonHang) {
            if (! $chiTietDonHang->donHang) {
                continue;
            }

            $trangThaiDuocTinh = [
                'hoan_thanh',
                
            ];

            if (! in_array($chiTietDonHang->donHang->trang_thai, $trangThaiDuocTinh)) {
                continue;
            }

            $soLuongDaBan += (int) $chiTietDonHang->so_luong;
        }

        return $soLuongDaBan;
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
            $tenSanPham = preg_replace('/\s*'.preg_quote($tenBienThe, '/').'$/iu', '', $tenSanPham);
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
        $donVi = trim((string) $this->don_vi);

        if ($donVi != '') {
            return str_replace(' ', '', $donVi);
        }

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

        return trim($this->ten_goc.' '.$this->ten_bien_the);
    }

    // Lấy đường dẫn hình ảnh chính của sản phẩm.
    public function getDuongDanHinhAnhAttribute()
    {
        if ($this->hinhAnhDauTien && $this->hinhAnhDauTien->hinh_anh) {
            return asset('storage/'.$this->hinhAnhDauTien->hinh_anh);
        }

        $hinhAnhThayThe = $this->layHinhAnhBienTheThayThe();
        if ($hinhAnhThayThe) {
            return asset('storage/'.$hinhAnhThayThe->hinh_anh);
        }

        return asset('storage/uploads/products/default.png');
    }

    // Tính tổng tiền theo số lượng mua và giá đang bán của sản phẩm.
    public function tinhGiaTheoSoLuong($soLuong)
    {
        $soLuong = max(0, (int) $soLuong);

        return $soLuong * (float) $this->gia_hien_tai;
    }

    // Tính đơn giá trung bình cho số lượng sản phẩm đang mua.
    public function layDonGiaTheoSoLuong($soLuong)
    {
        $soLuong = max(1, (int) $soLuong);

        return $this->tinhGiaTheoSoLuong($soLuong) / $soLuong;
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

    // Tạo truy vấn lấy các lô còn hàng và chưa hết hạn.
    public function layCacLoHangCoTheBan()
    {
        return $this->loHangKhos()
            ->where('so_luong_con', '>', 0)
            ->where(function ($query) {
                $query->whereNull('han_su_dung')
                    ->orWhereDate('han_su_dung', '>=', today());
            })
            ->orderBy('han_su_dung')
            ->orderBy('ma_lo_hang_kho');
    }

    // Tính tổng số lượng còn có thể bán.
    public function soLuongCoTheBan()
    {
        if (! $this->loHangKhos()->exists()) {
            return (int) $this->ton_kho;
        }

        return (int) $this->layCacLoHangCoTheBan()->sum('so_luong_con');
    }

    // Trừ tồn kho khi khách đặt hàng và trả về phần phân bổ theo lô.
    public function truTonKho($soLuong, $tinhCaHangHetHan = false)
    {
        $soLuong = (int) $soLuong;
        if ($soLuong <= 0) {
            return [];
        }

        $soLuongCoSan = $this->soLuongCoTheBan();
        if ($tinhCaHangHetHan) {
            $soLuongCoSan = (int) $this->ton_kho;
        }

        if ($soLuongCoSan < $soLuong) {
            throw new Exception('Sản phẩm "'.$this->ten_hien_thi.'" không đủ hàng.');
        }

        $phanBoTonKhos = $this->truTonKhoTheoLo($soLuong, $tinhCaHangHetHan);
        $this->ton_kho -= $soLuong;
        $this->capNhatTrangThaiTonKho();
        $this->save();

        return $phanBoTonKhos;
    }

    // Hoàn lại tồn kho khi đơn hàng bị hủy hoặc hàng được trả lại nguyên vẹn.
    public function hoanTonKho($soLuong, $phanBoTonKhos = [])
    {
        $soLuong = (int) $soLuong;
        if ($soLuong <= 0) {
            return;
        }

        foreach ($phanBoTonKhos as $phanBoTonKho) {
            $maLoHangKho = $phanBoTonKho['ma_lo_hang_kho'] ?? null;
            $maChiTietPhieuNhap = $phanBoTonKho['ma_chi_tiet_phieu_nhap'] ?? null;
            $soLuongHoan = (int) ($phanBoTonKho['so_luong'] ?? 0);

            if ($soLuongHoan <= 0) {
                continue;
            }

            if ($maLoHangKho) {
                $loHangKho = LoHangKho::lockForUpdate()->find($maLoHangKho);
            } else {
                $loHangKho = LoHangKho::where('ma_chi_tiet_phieu_nhap', $maChiTietPhieuNhap)
                    ->lockForUpdate()
                    ->first();
            }

            if ($loHangKho && (int) $loHangKho->ma_san_pham == (int) $this->ma_san_pham) {
                $loHangKho->so_luong_con += $soLuongHoan;
                $loHangKho->save();
            }
        }

        $this->ton_kho += $soLuong;
        $this->capNhatTrangThaiTonKho();
        $this->save();
    }

    // Cập nhật trạng thái còn hàng hoặc hết hàng.
    public function capNhatTrangThaiTonKho()
    {
        if ($this->ton_kho > 0 && $this->soLuongCoTheBan() > 0) {
            $this->trang_thai = 'con_hang';
        } else {
            $this->trang_thai = 'het_hang';
        }
    }

    // Trừ số lượng trong từng lô kho theo thứ tự hết hạn sớm.
    private function truTonKhoTheoLo($soLuong, $tinhCaHangHetHan)
    {
        $soLuongCon = $soLuong;
        $phanBoTonKhos = [];
        $query = $this->loHangKhos()->where('so_luong_con', '>', 0);

        if (! $tinhCaHangHetHan) {
            $query->where(function ($query) {
                $query->whereNull('han_su_dung')
                    ->orWhereDate('han_su_dung', '>=', today());
            });
        }

        $loHangKhos = $query
            ->orderBy('han_su_dung')
            ->orderBy('ma_lo_hang_kho')
            ->lockForUpdate()
            ->get();

        foreach ($loHangKhos as $loHangKho) {
            if ($soLuongCon <= 0) {
                break;
            }

            $soLuongLay = min($soLuongCon, (int) $loHangKho->so_luong_con);
            if ($soLuongLay <= 0) {
                continue;
            }

            $loHangKho->so_luong_con -= $soLuongLay;
            $loHangKho->save();

            $phanBoTonKhos[] = [
                'ma_lo_hang_kho' => $loHangKho->ma_lo_hang_kho,
                'ma_chi_tiet_phieu_nhap' => $loHangKho->ma_chi_tiet_phieu_nhap,
                'so_luong' => $soLuongLay,
                'gia' => $this->layGiaTheoLo($loHangKho),
            ];
            $soLuongCon -= $soLuongLay;
        }

        if ($soLuongCon > 0) {
            $phanBoTonKhos[] = [
                'ma_chi_tiet_phieu_nhap' => null,
                'so_luong' => $soLuongCon,
            ];
        }

        return $phanBoTonKhos;
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
            if ($sanPham->hinhAnhDauTien
                && mb_strtolower($sanPham->ten_goc, 'UTF-8') == $tenGoc) {
                return $sanPham->hinhAnhDauTien;
            }
        }

        return null;
    }
}