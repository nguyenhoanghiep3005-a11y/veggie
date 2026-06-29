<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GhnLocationController extends Controller
{
    public function provinces()
    {
        if (!$this->hashToken()) {
            return response()->json(['status' => false, 'message' => 'Token GHN chưa được cấu hình'], 400);
        }
        // Gọi API GHN để lấy danh sách tỉnh thành (bỏ qua xác thực SSL trên local)
        $response = Http::withoutVerifying()->withHeaders($this->headers())->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province');
        return $this->formatResponse($response);
    }

    public function districts(Request $request)
    {
        if (!$this->hashToken()) {
            return response()->json(['status' => false, 'message' => 'Token GHN chưa được cấu hình'], 400);
        }
        // Gọi API GHN để lấy danh sách tỉnh thành (bỏ qua xác thực SSL trên local)
        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district', ['province_id' => (int) $request->province_id]);
        return $this->formatResponse($response);
    }
    public function wards(Request $request)
    {
        if (!$this->hashToken()) {
            return response()->json(['status' => false, 'message' => 'Token GHN chưa được cấu hình'], 400);
        }
        // Gọi API GHN để lấy danh sách tỉnh thành (bỏ qua xác thực SSL trên local)
        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->get(
                'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward',
                ['district_id' => (int) $request->district_id],
            );
        return $this->formatResponse($response);
    }
    private function hashToken()
    {
        return (bool) config('ghn.token');
    }
    private function headers()
    {
        return [
            'Token' => config('ghn.token'),
            'Content-Type' => 'application/json',
        ];
    }
    private function formatResponse($response)
    {
        if ($response->successful() && isset($response['data'])) {
            return response()->json([
                'status' => true,
                'data' => $response['data'],
            ]);
        }
        return response()->json(
            [
                'status' => false,
                'message' => 'Lỗi khi gọi API GHN',
                'details' => $response->json(),
            ],
            $response->status() ?: 400,
        );
    }
}
