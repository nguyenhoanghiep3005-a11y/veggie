<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HoaDonDonHangMail extends Mailable
{
    use Queueable, SerializesModels;

    public $donHang;
    public $tamTinh;
    public $phiVanChuyen;
    public $ngayDatHang;
    public $tenKhachHang;
    public $tenNguoiNhan;
    public $soDienThoaiNguoiNhan;
    public $diaChiNguoiNhan;
    public $tinhThanhNguoiNhan;

    // Khoi tao va chuan bi du lieu hien thi tren hoa don.
    public function __construct($donHang)
    {
        $donHang->load(['nguoiDung', 'diaChiGiaoHang', 'chiTietDonHangs.sanPham']);
        $this->donHang = $donHang;
        $this->chuanBiChiTietDonHang();
        $this->chuanBiTongTien();
        $this->chuanBiThongTinNguoiNhan();
    }

    // Tao email hoa don cua don hang.
    public function build()
    {
        return $this->subject('Hóa đơn đặt hàng #'.$this->donHang->ma_don_hang)
            ->view('admin.emails.hoa-don');
    }

    // Chuan bi ten san pham va thanh tien cua tung dong.
    private function chuanBiChiTietDonHang()
    {
        foreach ($this->donHang->chiTietDonHangs as $chiTietDonHang) {
            $chiTietDonHang->ten_san_pham = 'Sản phẩm';

            if ($chiTietDonHang->sanPham) {
                $chiTietDonHang->ten_san_pham = $chiTietDonHang->sanPham->ten_hien_thi;
            }

            $chiTietDonHang->thanh_tien = $chiTietDonHang->so_luong * $chiTietDonHang->gia;
        }
    }

    // Chuan bi tam tinh va phi van chuyen cua don hang.
    private function chuanBiTongTien()
    {
        $this->tamTinh = (float) $this->donHang->tam_tinh;

        if ($this->tamTinh <= 0) {
            foreach ($this->donHang->chiTietDonHangs as $chiTietDonHang) {
                $this->tamTinh += $chiTietDonHang->thanh_tien;
            }
        }

        $this->phiVanChuyen = (float) $this->donHang->phi_van_chuyen;

        if ($this->phiVanChuyen <= 0) {
            $this->phiVanChuyen =
                (float) $this->donHang->tong_tien
                - $this->tamTinh
                + (float) $this->donHang->so_tien_giam;

            if ($this->phiVanChuyen < 0) {
                $this->phiVanChuyen = 0;
            }
        }

        $this->ngayDatHang = '';

        if ($this->donHang->created_at) {
            $this->ngayDatHang = $this->donHang->created_at->format('d/m/Y H:i');
        }
    }

    // Chuan bi ten, so dien thoai va dia chi nguoi nhan.
    private function chuanBiThongTinNguoiNhan()
    {
        $this->tenKhachHang = 'quý khách';
        $this->tenNguoiNhan = '-';
        $this->soDienThoaiNguoiNhan = '-';
        $this->diaChiNguoiNhan = '-';
        $this->tinhThanhNguoiNhan = '';

        if ($this->donHang->nguoiDung) {
            $this->tenKhachHang = $this->donHang->nguoiDung->ten;
        }

        $diaChiGiaoHang = $this->donHang->layDiaChiGiaoHang();

        if (! $diaChiGiaoHang) {
            return;
        }

        if (! $this->donHang->nguoiDung && $diaChiGiaoHang->ho_ten) {
            $this->tenKhachHang = $diaChiGiaoHang->ho_ten;
        }

        if ($diaChiGiaoHang->ho_ten) {
            $this->tenNguoiNhan = $diaChiGiaoHang->ho_ten;
        }

        if ($diaChiGiaoHang->so_dien_thoai) {
            $this->soDienThoaiNguoiNhan = $diaChiGiaoHang->so_dien_thoai;
        }

        if ($diaChiGiaoHang->dia_chi) {
            $this->diaChiNguoiNhan = $diaChiGiaoHang->dia_chi;
        }

        if ($diaChiGiaoHang->tinh_thanh) {
            $this->tinhThanhNguoiNhan = ', '.$diaChiGiaoHang->tinh_thanh;
        }
    }
}