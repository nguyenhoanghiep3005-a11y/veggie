<?php

namespace App\Services;

use App\Models\DiaChiGiaoHang;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhiVanChuyenGhnService
{
    // Tinh phi van chuyen thuc te tu API GHN.
    public function tinhPhiVanChuyen($diaChi, $sanPhamGioHangs)
    {
        if (! $this->coTheTinhPhi($diaChi)) {
            throw new Exception('Cấu hình GHN hoặc địa chỉ giao hàng chưa đầy đủ.');
        }

        $data = $this->taoDuLieuTinhPhi($diaChi, $sanPhamGioHangs);

        try {
            $response = Http::withoutVerifying()
                ->timeout(8)
                ->withHeaders([
                    'Token' => config('ghn.token'),
                    'ShopId' => config('ghn.shop_id'),
                    'Content-Type' => 'application/json',
                ])
                ->post(config('ghn.api_url'), $data);
        } catch (Exception $exception) {
            Log::error('Lỗi kết nối API tính phí GHN: '.$exception->getMessage());

            throw new Exception('Không thể kết nối GHN để tính phí vận chuyển.');
        }

        if ($response->successful() && isset($response['data']['total'])) {
            return (int) $response['data']['total'];
        }

        $thongBao = 'GHN không tính được phí cho địa chỉ đang chọn.';
        $phanHoi = $response->json();

        if (isset($phanHoi['message']) && $phanHoi['message']) {
            $thongBao = $phanHoi['message'];
        }

        Log::warning('Không lấy được phí vận chuyển GHN.', [
            'ma_trang_thai' => $response->status(),
            'phan_hoi' => $phanHoi,
        ]);

        throw new Exception($thongBao);
    }

    // Kiem tra cau hinh va dia chi da du de tinh phi GHN.
    private function coTheTinhPhi($diaChi)
    {
        if (! $diaChi instanceof DiaChiGiaoHang) {
            return false;
        }

        if (! config('ghn.token') || ! config('ghn.shop_id')) {
            return false;
        }

        if (! config('ghn.from_district_id') || ! config('ghn.from_ward_id')) {
            return false;
        }

        return $diaChi->coDiaChiGhn();
    }

    // Tao cac truong du lieu theo dung tai lieu API GHN.
    private function taoDuLieuTinhPhi($diaChi, $sanPhamGioHangs)
    {
        $khoiLuong = (int) config('ghn.default_weight', 500);
        $chieuDai = (int) config('ghn.default_length', 20);
        $chieuRong = (int) config('ghn.default_width', 15);
        $chieuCao = (int) config('ghn.default_height', 5);

        $data = [
            'from_district_id' => (int) config('ghn.from_district_id'),
            'from_ward_code' => (string) config('ghn.from_ward_id'),
            'to_district_id' => (int) $diaChi->ma_huyen,
            'to_ward_code' => (string) $diaChi->ma_xa,
            'service_id' => $this->layMaDichVu($diaChi),
            'height' => $chieuCao,
            'length' => $chieuDai,
            'width' => $chieuRong,
            'weight' => $khoiLuong,
            'insurance_value' => (int) $this->tinhTamTinh($sanPhamGioHangs),
            'cod_failed_amount' => 0,
            'items' => [
                [
                    'name' => 'Sản phẩm HiepShop',
                    'quantity' => $this->tinhTongSoLuong($sanPhamGioHangs),
                    'height' => $chieuCao,
                    'length' => $chieuDai,
                    'width' => $chieuRong,
                    'weight' => $khoiLuong,
                ],
            ],
        ];

        $data['service_type_id'] = (int) config('ghn.service_type_id', 2);

        return $data;
    }

    // Lay ma dich vu GHN phu hop voi quan gui va quan nhan.
    private function layMaDichVu($diaChi)
    {
        $response = Http::withoutVerifying()
            ->timeout(10)
            ->withHeaders([
                'Token' => config('ghn.token'),
                'Content-Type' => 'application/json',
            ])
            ->post(config('ghn.available_services_url'), [
                'shop_id' => (int) config('ghn.shop_id'),
                'from_district' => (int) config('ghn.from_district_id'),
                'to_district' => (int) $diaChi->ma_huyen,
            ]);

        if (! $response->successful() || ! isset($response['data'])) {
            throw new Exception('GHN không tìm được dịch vụ giao hàng cho địa chỉ đang chọn.');
        }

        $cacDichVu = $response['data'];
        $loaiDichVu = (int) config('ghn.service_type_id', 2);

        foreach ($cacDichVu as $dichVu) {
            if (
                isset($dichVu['service_type_id'])
                && (int) $dichVu['service_type_id'] == $loaiDichVu
                && isset($dichVu['service_id'])
            ) {
                return (int) $dichVu['service_id'];
            }
        }

        foreach ($cacDichVu as $dichVu) {
            if (isset($dichVu['service_id'])) {
                return (int) $dichVu['service_id'];
            }
        }

        throw new Exception('GHN không hỗ trợ giao hàng đến địa chỉ đang chọn.');
    }
    // Tinh tong tien san pham de khai gia tri bao hiem cho GHN.
    private function tinhTamTinh($sanPhamGioHangs)
    {
        $tamTinh = 0;

        foreach ($sanPhamGioHangs as $sanPhamGioHang) {
            if (isset($sanPhamGioHang['tam_tinh'])) {
                $tamTinh += $sanPhamGioHang['tam_tinh'];
            }
        }

        return $tamTinh;
    }

    // Tinh tong so luong san pham gui cho GHN.
    private function tinhTongSoLuong($sanPhamGioHangs)
    {
        $tongSoLuong = 0;

        foreach ($sanPhamGioHangs as $sanPhamGioHang) {
            if (isset($sanPhamGioHang['so_luong'])) {
                $tongSoLuong += (int) $sanPhamGioHang['so_luong'];
            }
        }

        if ($tongSoLuong < 1) {
            return 1;
        }

        return $tongSoLuong;
    }
}
