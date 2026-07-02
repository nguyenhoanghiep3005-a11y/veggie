<?php

namespace App\Services;

use App\Models\ShippingAddress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhnShippingService
{
    public function calculateFee(ShippingAddress $address, Collection|array $cartItems): int
    {
        $fallback = (int) config('ghn.fallback_fee', 25000);
        
        if (!$this->canQuote($address)) {
            return $fallback;
        }

        $payload = $this->buildPayload($address, $cartItems);

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Token' => config('ghn.token'),
                'ShopId' => config('ghn.shop_id'),
                'Content-Type' => 'application/json',
            ])->post(config('ghn.api_url', 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee'), $payload);

            if ($response->successful() && isset($response['data']['total'])) {
                return (int) $response['data']['total'];
            }

            Log::warning('GHN fee quote failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            Log::error('GHN fee quote error: ' . $e->getMessage());
        }

        return $fallback;
    }

    private function canQuote(ShippingAddress $address): bool
    {
        if (!config('ghn.token') || !config('ghn.shop_id')) {
            return false;
        }

        if (!config('ghn.from_district_id') || !config('ghn.from_ward_id')) {
            return false;
        }

        if (!$address->district_id || !$address->ward_id) {
            return false;
        }

        return true;
    }

    private function buildPayload(ShippingAddress $address, Collection|array $cartItems): array
    {
        $items = $cartItems instanceof Collection ? $cartItems : collect($cartItems);

        // Luôn sử dụng trọng lượng mặc định của một gói hàng, không cộng dồn cân nặng các sản phẩm
        $weight = (int) config('ghn.default_weight', 500);

        $subtotal = (int) $items->sum(function ($item) {
            if (isset($item->product) && method_exists($item->product, 'calculatePriceByQuantity')) {
                return $item->product->calculatePriceByQuantity($item->quantity ?? 1, $item->inventory_id ?? null);
            }
            $price = $item['price'] ?? $item->product->price ?? 0;
            $quantity = (int) ($item['quantity'] ?? $item->quantity ?? 1);
            return $price * $quantity;
        });

        $totalQuantity = (int) $items->sum(function ($item) {
            return (int) ($item['quantity'] ?? $item->quantity ?? 1);
        }) ?: 1;

        $payload = [
            'from_district_id' => (int) config('ghn.from_district_id'),
            'from_ward_code' => (string) config('ghn.from_ward_id'),
            'to_district_id' => (int) $address->district_id,
            'to_ward_code' => (string) $address->ward_id,
            'height' => (int) config('ghn.default_height', 10),
            'length' => (int) config('ghn.default_length', 10),
            'width' => (int) config('ghn.default_width', 10),
            'weight' => $weight,
            'insurance_value' => $subtotal,
            'cod_failed_amount' => 0
        ];

        $serviceId = $this->resolveServiceId($address);

        if (!$serviceId && config('ghn.service_id') && config('ghn.service_id') != 123456) {
            $serviceId = config('ghn.service_id');
        }

        if ($serviceId) {
            $payload['service_id'] = (int) $serviceId;
        } elseif (in_array((int) config('ghn.service_type_id'), [2, 5])) {
            $payload['service_type_id'] = (int) config('ghn.service_type_id');
        }

        $payload['items'] = [
            [
                'name' => 'Gói hàng',
                'quantity' => 1,
                'height' => (int) config('ghn.default_height', 10),
                'length' => (int) config('ghn.default_length', 10),
                'width' => (int) config('ghn.default_width', 10),
                'weight' => $weight,
            ]
        ];

        return $payload;
    }

    private function resolveServiceId(ShippingAddress $address): ?int
    {
        try {
            $res = Http::withoutVerifying()->withHeaders([
                'Token' => config('ghn.token'),
                'ShopId' => config('ghn.shop_id'),
                'Content-Type' => 'application/json',
            ])->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                'shop_id' => (int) config('ghn.shop_id'),
                'from_district' => (int) config('ghn.from_district_id'),
                'to_district' => (int) $address->district_id,
            ]);

            if ($res->successful() && isset($res['data'][0]['service_id'])) {
                return (int) $res['data'][0]['service_id'];
            }

            Log::warning('GHN available services failed', [
                'status' => $res->status(),
                'body' => $res->json(),
            ]);
        } catch (\Throwable $e) {
            Log::error('GHN resolve service error: ' . $e->getMessage());
        }

        return null;
    }
}
