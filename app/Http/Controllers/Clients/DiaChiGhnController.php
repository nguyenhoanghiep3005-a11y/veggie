<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DiaChiGhnController extends Controller
{
    // Lay danh sach tinh thanh tu GHN.
    public function layTinhThanh()
    {
        return $this->goiApiGhn(
            'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province'
        );
    }

    // Lay danh sach quan huyen theo ma tinh thanh.
    public function layHuyen(Request $request)
    {
        $data = $request->validate([
            'ma_tinh' => 'required|integer',
        ]);

        return $this->goiApiGhn(
            'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district',
            ['province_id' => (int) $data['ma_tinh']]
        );
    }

    // Lay danh sach phuong xa theo ma quan huyen.
    public function layXa(Request $request)
    {
        $data = $request->validate([
            'ma_huyen' => 'required|integer',
        ]);

        return $this->goiApiGhn(
            'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward',
            ['district_id' => (int) $data['ma_huyen']]
        );
    }

    // Goi API GHN va tra ve mot cau truc du lieu thong nhat.
    private function goiApiGhn($duongDan, $data = [])
    {
        if (! config('ghn.token')) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Token GHN chưa được cấu hình.',
            ], 422);
        }

        $response = Http::withoutVerifying()
            ->timeout(8)
            ->withHeaders([
                'Token' => config('ghn.token'),
                'Content-Type' => 'application/json',
            ])
            ->get($duongDan, $data);

        if ($response->successful() && isset($response['data'])) {
            return response()->json([
                'trang_thai' => true,
                'du_lieu' => $response['data'],
            ]);
        }

        $thongBao = 'Không thể gọi API GHN. Vui lòng kiểm tra lại cấu hình.';
        $phanHoi = $response->json();

        if (isset($phanHoi['message']) && $phanHoi['message']) {
            $thongBao = $phanHoi['message'];
        }

        return response()->json([
            'trang_thai' => false,
            'thong_bao' => $thongBao,
        ], $response->status());
    }
}
