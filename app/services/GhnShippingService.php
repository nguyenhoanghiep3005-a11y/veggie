<?php

namespace App\Services;

use App\Models\ShippingAddress;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhnShippingService
{
    // Tính phí giao hàng GHN, nếu API lỗi thì dùng phí mặc định.
    public function calculateFee($address, $cartItems)
    {
        $fallback = (int) config('ghn.fallback_fee', 25000);

        if (! $this->canQuote($address)) {
            return $fallback;
        }

        $payload = $this->buildPayload($address, $cartItems);

        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->retry(1, 200)
                ->withHeaders([
                    'Token' => config('ghn.token'),
                    'ShopId' => config('ghn.shop_id'),
                    'Content-Type' => 'application/json',
                ])
                ->post(config('ghn.api_url', 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee'), $payload);

            if ($response->successful() && isset($response['data']['total'])) {
                return (int) $response['data']['total'];
            }

            Log::warning('Không lấy được phí giao hàng GHN', [
                'status' => $response->status(),
                'body' => $response->json(),
                'payload' => $payload,
            ]);
        } catch (Exception $exception) {
            Log::error('Lỗi gọi phí giao hàng GHN: '.$exception->getMessage());
        }

        return $fallback;
    }

    // Kiểm tra đã đủ cấu hình và địa chỉ để gọi phí GHN chưa.
    private function canQuote($address)
    {
        if (! $address instanceof ShippingAddress) {
            return false;
        }

        if (! config('ghn.token') || ! config('ghn.shop_id')) {
            return false;
        }

        if (! config('ghn.from_district_id') || ! config('ghn.from_ward_id')) {
            return false;
        }

        if (! $address->district_id || ! $address->ward_id) {
            return false;
        }

        return true;
    }

    // Tạo dữ liệu gửi lên GHN để lấy phí giao hàng.
    private function buildPayload($address, $cartItems)
    {
        $weight = (int) config('ghn.default_weight', 500);
        $subtotal = $this->cartSubtotal($cartItems);
        $serviceId = $this->resolveServiceId($address);

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
            'cod_failed_amount' => 0,
        ];

        if (! $serviceId && config('ghn.service_id') && config('ghn.service_id') != 123456) {
            $serviceId = config('ghn.service_id');
        }

        if ($serviceId) {
            $payload['service_id'] = (int) $serviceId;
        } else {
            $serviceTypeId = (int) config('ghn.service_type_id');
            if ($serviceTypeId == 2 || $serviceTypeId == 5) {
                $payload['service_type_id'] = $serviceTypeId;
            }
        }

        $payload['items'] = [
            [
                'name' => 'Gói hàng',
                'quantity' => 1,
                'height' => (int) config('ghn.default_height', 10),
                'length' => (int) config('ghn.default_length', 10),
                'width' => (int) config('ghn.default_width', 10),
                'weight' => $weight,
            ],
        ];

        return $payload;
    }

    // Tính tổng tiền sản phẩm để gửi làm giá trị bảo hiểm cho GHN.
    private function cartSubtotal($cartItems)
    {
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $product = null;
            $quantity = 1;
            $price = 0;

            if (is_object($item)) {
                if (isset($item->product)) {
                    $product = $item->product;
                }

                if (isset($item->quantity)) {
                    $quantity = (int) $item->quantity;
                }
            } else {
                if (isset($item['product'])) {
                    $product = $item['product'];
                }

                if (isset($item['quantity'])) {
                    $quantity = (int) $item['quantity'];
                }

                if (isset($item['price'])) {
                    $price = (float) $item['price'];
                }
            }

            if ($product && method_exists($product, 'calculatePriceByQuantity')) {
                $subtotal += $product->calculatePriceByQuantity($quantity);
                continue;
            }

            if ($product && isset($product->price)) {
                $price = (float) $product->price;
            }

            $subtotal += $price * $quantity;
        }

        return (int) $subtotal;
    }

    // Lấy service_id GHN phù hợp với quận/huyện nhận hàng và lưu tạm vào cache.
    private function resolveServiceId($address)
    {
        $cacheKey = 'ghn_service_id:'.config('ghn.from_district_id').':'.$address->district_id;

        $cachedServiceId = Cache::get($cacheKey);
        if ($cachedServiceId) {
            return (int) $cachedServiceId;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->retry(1, 200)
                ->withHeaders([
                    'Token' => config('ghn.token'),
                    'ShopId' => config('ghn.shop_id'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                    'shop_id' => (int) config('ghn.shop_id'),
                    'from_district' => (int) config('ghn.from_district_id'),
                    'to_district' => (int) $address->district_id,
                ]);

            if ($response->successful() && isset($response['data'][0]['service_id'])) {
                $serviceId = (int) $response['data'][0]['service_id'];
                Cache::put($cacheKey, $serviceId, now()->addDay());

                return $serviceId;
            }

            Log::warning('Không lấy được dịch vụ GHN', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        } catch (Exception $exception) {
            Log::error('Lỗi lấy dịch vụ GHN: '.$exception->getMessage());
        }

        return null;
    }
}
