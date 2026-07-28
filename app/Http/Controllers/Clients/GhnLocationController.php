<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GhnLocationController extends Controller
{
    public function provinces()
    {
        if (! $this->hasToken()) {
            return response()->json([
                'status' => false,
                'message' => 'Token GHN chưa được cấu hình.',
            ]);
        }

        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province');

        return $this->formatResponse($response);
    }

    public function districts(Request $request)
    {
        if (! $request->province_id) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng chọn tỉnh/thành.',
            ]);
        }

        if (! $this->hasToken()) {
            return response()->json([
                'status' => false,
                'message' => 'Token GHN chưa được cấu hình.',
            ]);
        }

        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                'province_id' => (int) $request->province_id,
            ]);

        return $this->formatResponse($response);
    }

    public function wards(Request $request)
    {
        if (! $request->district_id) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng chọn quận/huyện.',
            ]);
        }

        if (! $this->hasToken()) {
            return response()->json([
                'status' => false,
                'message' => 'Token GHN chưa được cấu hình.',
            ]);
        }

        $response = Http::withoutVerifying()
            ->withHeaders($this->headers())
            ->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                'district_id' => (int) $request->district_id,
            ]);

        return $this->formatResponse($response);
    }

    private function hasToken(): bool
    {
        return filled(config('ghn.token'));
    }

    private function headers(): array
    {
        return [
            'Token' => config('ghn.token'),
            'Content-Type' => 'application/json',
        ];
    }

    private function formatResponse(Response $response)
    {
        if ($response->successful() && isset($response['data'])) {
            return response()->json([
                'status' => true,
                'data' => $response['data'],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $this->errorMessage($response),
            'details' => $response->json(),
        ]);
    }

    private function errorMessage(Response $response): string
    {
        $message = (string) data_get($response->json(), 'message');

        if ($response->status() === 401 && str_contains($message, 'IP') && str_contains($message, 'not valid')) {
            return 'GHN đang chặn IP hiện tại. Vui lòng cập nhật IP được phép trong tài khoản GHN hoặc dùng token/shop GHN khác.';
        }

        return 'Không thể gọi API GHN. Vui lòng kiểm tra token, shop id hoặc cấu hình GHN.';
    }
}
