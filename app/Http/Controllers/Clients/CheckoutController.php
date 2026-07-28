<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Services\CartService;
use App\Services\GhnShippingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected GhnShippingService $shippingService,
        protected CartService $cart
    ) {}

    // Hiển thị trang checkout, lấy giỏ hàng, địa chỉ, voucher và tổng tiền.
    public function index()
    {
        $user = Auth::user();
        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            toastr()->error('Giỏ hàng trống');

            return redirect()->route('cart.index');
        }

        $addresses = [];
        if ($user) {
            $addresses = ShippingAddress::where('user_id', $user->id)->get();
        }

        $defaultAddress = $this->defaultAddress($addresses);
        $coupon = $this->sessionCoupon();
        $amounts = $this->calculateAmounts($cartItems, $defaultAddress, $coupon);

        $claimedCoupons = [];
        if ($user) {
            $userCoupons = $user->coupons()
                ->usable()
                ->orderByDesc('coupon_user.claimed_at')
                ->get();

            foreach ($userCoupons as $userCoupon) {
                if (! $userCoupon->pivot->used_at) {
                    $claimedCoupons[] = $userCoupon;
                }
            }
        }

        $subtotal = $amounts['subtotal'];
        $shippingFee = $amounts['shipping_fee'];
        $discount = $amounts['discount'];
        $totalPrice = $amounts['total'];

        return view('clients.pages.checkout', compact(
            'addresses',
            'defaultAddress',
            'cartItems',
            'subtotal',
            'shippingFee',
            'discount',
            'totalPrice',
            'coupon',
            'claimedCoupons'
        ));
    }

    // Lấy thông tin địa chỉ đã lưu khi khách chọn địa chỉ tài khoản.
    public function getAddress(Request $request)
    {
        $data = $request->validate([
            'address_id' => 'required|integer|exists:shipping_addresses,id',
        ]);

        $address = ShippingAddress::where('id', $data['address_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (! $address) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa chỉ giao hàng.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $address->id,
                'full_name' => $address->full_name,
                'phone' => $address->phone,
                'address' => $address->address,
                'city' => $address->city,
                'has_ghn_location' => $address->hasGhnLocation(),
            ],
        ]);
    }

    // Kiểm tra mã giảm giá và lưu mã hợp lệ vào session checkout.
    public function applyCoupon(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng mã giảm giá.',
            ], 403);
        }

        $data = $request->validate([
            'code' => 'required|string|max:50',
            'address_id' => 'nullable|integer|exists:shipping_addresses,id',
        ]);

        $couponCode = trim($data['code']);
        $coupon = Coupon::usable()
            ->where('code', $couponCode)
            ->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại, hết hạn hoặc đã hết lượt dùng.',
            ], 422);
        }

        $cartItems = $this->cart->items();
        $subtotal = $this->cartSubtotal($cartItems);

        if ($error = $coupon->validateForUser(Auth::id(), $subtotal)) {
            return response()->json([
                'success' => false,
                'message' => $error,
            ], 422);
        }

        session()->put('checkout_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
        ]);

        $addressId = null;
        if (isset($data['address_id'])) {
            $addressId = $data['address_id'];
        }

        $address = $this->findUserAddress(Auth::id(), $addressId);
        $amounts = $this->calculateAmounts($cartItems, $address, $coupon);

        return response()->json([
            'success' => true,
            'message' => "Đã áp dụng mã {$coupon->code}.",
            'coupon' => $coupon->code,
            'discount' => $amounts['discount'],
            'total' => $amounts['total'],
            'formatted_discount' => number_format($amounts['discount'], 0, ',', '.').' đ',
            'formatted_total' => number_format($amounts['total'], 0, ',', '.').' đ',
        ]);
    }

    // Tính phí ship cho địa chỉ đã lưu trong tài khoản.
    public function shippingFee(Request $request)
    {
        $data = $request->validate([
            'address_id' => 'required|integer|exists:shipping_addresses,id',
        ]);

        $address = $this->findUserAddress(Auth::id(), $data['address_id']);
        if (! $address) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy địa chỉ giao hàng.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->calculateAmounts($this->cart->items(), $address, $this->sessionCoupon()),
        ]);
    }

    // Tính phí ship cho địa chỉ khách nhập mới ở trang checkout.
    public function shippingFeeGuest(Request $request)
    {
        $data = $request->validate([
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
        ]);

        $address = new ShippingAddress([
            'province_id' => $data['province_id'],
            'district_id' => $data['district_id'],
            'ward_id' => $data['ward_id'],
        ]);

        return response()->json([
            'status' => true,
            'data' => $this->calculateAmounts($this->cart->items(), $address, null),
        ]);
    }

    // Đặt hàng COD cho user đã đăng nhập và dùng địa chỉ tài khoản.
    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            toastr()->error('Khách vãng lai chỉ được thanh toán bằng PayPal.');

            return redirect()->route('checkout');
        }

        if ($request->input('delivery_type') !== 'account') {
            toastr()->error('Địa chỉ khác hoặc đặt cho người thân chỉ được thanh toán bằng PayPal.');

            return redirect()->route('checkout')->withInput();
        }

        $data = $this->validateCheckout($request, true);
        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            toastr()->error('Giỏ hàng trống');

            return redirect()->route('cart.index');
        }

        $addressInfo = $this->resolveAddress($data, $request, $user->id);
        if (! $addressInfo['address'] || ! $addressInfo['address']->hasGhnLocation()) {
            toastr()->error('Vui lòng chọn đầy đủ tỉnh, quận, phường GHN.');

            return redirect()->route('checkout');
        }

        $shippingFee = $this->getShippingFee($addressInfo['address'], $cartItems);

        try {
            DB::transaction(function () use ($cartItems, $data, $addressInfo, $user, $shippingFee) {
                $order = $this->createOrder($user->id, $addressInfo);
                $itemsTotal = $this->createOrderItemsAndUpdateStock($order, $cartItems);
                $coupon = $this->lockedSessionCoupon($itemsTotal);
                $discount = 0;
                $couponId = null;
                $couponCode = null;

                if ($coupon) {
                    $discount = $coupon->discountAmount($itemsTotal);
                    $couponId = $coupon->id;
                    $couponCode = $coupon->code;
                }

                $order->update([
                    'subtotal' => $itemsTotal,
                    'shipping_fee' => $shippingFee,
                    'discount_amount' => $discount,
                    'total_price' => max(0, $itemsTotal - $discount + $shippingFee),
                    'coupon_id' => $couponId,
                    'coupon_code' => $couponCode,
                ]);

                if ($coupon) {
                    $coupon->increment('used_count');
                }
                $this->markClaimedCouponUsed($coupon);

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $data['payment_method'],
                    'amount' => $order->total_price,
                    'status' => 'pending',
                ]);
            });

            $this->cart->clear();
            session()->forget(['checkout_coupon', 'checkout_shipping_quote']);
            toastr()->success('Đặt hàng thành công!');

            return redirect()->route('account');
        } catch (Exception $e) {
            Log::error('Lỗi đặt hàng COD: '.$e->getMessage());
            toastr()->error($e->getMessage() ?: 'Có lỗi xảy ra, vui lòng thử lại.');

            return redirect()->route('checkout');
        }
    }

    // Đặt hàng PayPal cho khách vãng lai hoặc địa chỉ nhập mới.
    public function placeOrderPayPal(Request $request)
    {
        $user = Auth::user();
        $data = $this->validateCheckout($request, false);
        $cartItems = $this->cart->items();
        $orderUserId = null;

        if (isset($data['delivery_type']) && $data['delivery_type'] === 'account' && $user) {
            $orderUserId = $user->id;
        }

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống.',
            ], 422);
        }

        $userId = null;
        if ($user) {
            $userId = $user->id;
        }

        $addressInfo = $this->resolveAddress($data, $request, $userId);
        if (! $addressInfo['address'] || ! $addressInfo['address']->hasGhnLocation()) {
            return response()->json([
                'success' => false,
                'message' => 'Địa chỉ giao hàng không hợp lệ.',
            ], 422);
        }

        $shippingFee = $this->getShippingFee($addressInfo['address'], $cartItems);

        try {
            DB::transaction(function () use ($cartItems, $data, $addressInfo, $orderUserId, $shippingFee) {
                $order = $this->createOrder($orderUserId, $addressInfo);
                $itemsTotal = $this->createOrderItemsAndUpdateStock($order, $cartItems);
                $coupon = $this->lockedSessionCoupon($itemsTotal);
                $discount = 0;
                $couponId = null;
                $couponCode = null;

                if ($coupon) {
                    $discount = $coupon->discountAmount($itemsTotal);
                    $couponId = $coupon->id;
                    $couponCode = $coupon->code;
                }

                $order->update([
                    'subtotal' => $itemsTotal,
                    'shipping_fee' => $shippingFee,
                    'discount_amount' => $discount,
                    'total_price' => max(0, $itemsTotal - $discount + $shippingFee),
                    'coupon_id' => $couponId,
                    'coupon_code' => $couponCode,
                ]);

                if ($coupon) {
                    $coupon->increment('used_count');
                }
                $this->markClaimedCouponUsed($coupon);

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'paypal',
                    'transaction_id' => $data['transactionID'],
                    'amount' => $order->total_price,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
            });

            $this->cart->clear();
            session()->forget(['checkout_coupon', 'checkout_shipping_quote']);

            return response()->json([
                'success' => true,
                'redirect_url' => $orderUserId ? route('account') : route('home'),
            ]);
        } catch (Exception $e) {
            Log::error('Lỗi đặt hàng PayPal: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Có lỗi xảy ra, vui lòng thử lại.',
            ], 500);
        }
    }

    // Gom rule validate cho COD và PayPal.
    private function validateCheckout(Request $request, bool $requireLogin)
    {
        $deliveryType = $request->input('delivery_type', $requireLogin ? 'account' : 'new');

        $rules = [
            'delivery_type' => 'required|in:account,new',
        ];

        if ($requireLogin) {
            $rules['payment_method'] = 'required|in:cash';
        } else {
            $rules['transactionID'] = 'required|string';
        }

        if ($deliveryType === 'account' && Auth::check()) {
            $rules['address_id'] = 'required|integer|exists:shipping_addresses,id';
        } else {
            $rules['guest_name'] = 'required|string|min:2|max:100';
            $rules['guest_phone'] = 'required|regex:/^[0-9]{10,11}$/';
            $rules['guest_address'] = 'required|string|min:5';
            $rules['guest_province_id'] = 'required';
            $rules['guest_district_id'] = 'required';
            $rules['guest_ward_id'] = 'required';
        }

        return $request->validate($rules);
    }

    // Chuẩn bị địa chỉ giao hàng từ địa chỉ tài khoản hoặc form nhập mới.
    private function resolveAddress(array $data, Request $request, $userId)
    {
        if (isset($data['delivery_type']) && $data['delivery_type'] === 'account' && $userId) {
            $addressId = null;
            if (isset($data['address_id'])) {
                $addressId = $data['address_id'];
            }

            $address = $this->findUserAddress($userId, $addressId);
            $addressIdValue = null;
            $addressData = null;

            if ($address) {
                $addressIdValue = $address->id;
                $addressData = [
                    'full_name' => $address->full_name,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'city' => $address->city,
                    'province_id' => $address->province_id,
                    'district_id' => $address->district_id,
                    'ward_id' => $address->ward_id,
                ];
            }

            return [
                'address' => $address,
                'address_id' => $addressIdValue,
                'address_data' => $addressData,
            ];
        }

        $addressData = [
            'full_name' => $data['guest_name'],
            'phone' => $data['guest_phone'],
            'address' => $data['guest_address'],
            'city' => $this->buildCityText($request),
            'province_id' => $data['guest_province_id'],
            'district_id' => $data['guest_district_id'],
            'ward_id' => $data['guest_ward_id'],
        ];

        return [
            'address' => new ShippingAddress($addressData),
            'address_id' => null,
            'address_data' => $addressData,
        ];
    }

    // Tạo đơn hàng rỗng trước, sau đó mới thêm sản phẩm và cập nhật tiền.
    private function createOrder($userId, array $addressInfo)
    {
        return Order::create([
            'user_id' => $userId,
            'shipping_address_id' => $addressInfo['address_id'],
            'shipping_address_data' => $addressInfo['address_data'],
            'subtotal' => 0,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'total_price' => 0,
            'status' => 'pending',
        ]);
    }

    // Thêm từng sản phẩm vào đơn hàng và trừ tồn kho.
    private function createOrderItemsAndUpdateStock(Order $order, $cartItems)
    {
        $total = 0;

        foreach ($cartItems as $item) {
            $product = Product::lockForUpdate()->find($item->product_id);
            $quantity = (int) $item->quantity;

            if (! $product || $quantity <= 0) {
                throw new Exception('Sản phẩm không tồn tại.');
            }

            if ($product->sellableStock() < $quantity) {
                throw new Exception('Sản phẩm "'.$product->display_name.'" không đủ hàng.');
            }

            $price = $product->unitPriceForQuantity($quantity);
            $allocations = $product->consumeStock($quantity);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'stock_allocations' => $allocations,
            ]);

            $total += $price * $quantity;
        }

        return $total;
    }

    // Tính tạm tính, phí ship, giảm giá và tổng tiền.
    private function calculateAmounts($cartItems, $address, $coupon)
    {
        $subtotal = $this->cartSubtotal($cartItems);
        $shippingFee = 0;
        $discount = 0;

        if ($address && $address->hasGhnLocation()) {
            $shippingFee = $this->getShippingFee($address, $cartItems);
        }

        if ($coupon) {
            $discount = $coupon->discountAmount($subtotal);
        }

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount + $shippingFee),
        ];
    }

    // Lấy phí ship GHN, có lưu tạm để tránh gọi lại nhiều lần.
    private function getShippingFee($address, $cartItems)
    {
        if (! $address || ! $address->hasGhnLocation()) {
            return 0;
        }

        $key = $this->shippingQuoteKey($address, $cartItems);
        $cachedQuote = session('checkout_shipping_quote');
        $cachedAt = 0;
        $cachedKey = null;

        if (is_array($cachedQuote)) {
            if (isset($cachedQuote['created_at'])) {
                $cachedAt = strtotime($cachedQuote['created_at']);
            }

            if (isset($cachedQuote['key'])) {
                $cachedKey = $cachedQuote['key'];
            }
        }

        $isFresh = $cachedAt && (now()->timestamp - $cachedAt <= 1800);

        if (is_array($cachedQuote) && $cachedKey === $key && $isFresh) {
            return (int) $cachedQuote['fee'];
        }

        $fee = $this->shippingService->calculateFee($address, $cartItems);

        session()->put('checkout_shipping_quote', [
            'key' => $key,
            'fee' => $fee,
            'created_at' => now()->toDateTimeString(),
        ]);

        return $fee;
    }

    // Tạo mã nhận diện địa chỉ và giỏ hàng để cache phí ship.
    private function shippingQuoteKey(ShippingAddress $address, $cartItems)
    {
        $addressKey = implode(':', [
            $address->id ?: 'guest',
            $address->province_id,
            $address->district_id,
            $address->ward_id,
        ]);

        return sha1($addressKey.'|'.$this->cartQuoteSignature($cartItems));
    }

    // Tạo chuỗi gồm id sản phẩm và số lượng trong giỏ hàng.
    private function cartQuoteSignature($cartItems)
    {
        $parts = [];

        foreach ($cartItems as $item) {
            $productId = 0;
            $quantity = 1;

            if (is_object($item)) {
                if (isset($item->product_id)) {
                    $productId = $item->product_id;
                }

                if (isset($item->quantity)) {
                    $quantity = $item->quantity;
                }
            } else {
                if (isset($item['product_id'])) {
                    $productId = $item['product_id'];
                }

                if (isset($item['quantity'])) {
                    $quantity = $item['quantity'];
                }
            }

            $parts[] = ((int) $productId).':'.((int) $quantity);
        }

        sort($parts);

        return implode('|', $parts);
    }

    // Chọn địa chỉ mặc định, ưu tiên địa chỉ có đủ mã GHN.
    private function defaultAddress($addresses)
    {
        if (count($addresses) == 0) {
            return null;
        }

        $defaultAddress = null;
        $firstAddress = null;
        $firstGhnAddress = null;

        foreach ($addresses as $address) {
            if (! $firstAddress) {
                $firstAddress = $address;
            }

            if ($address->default == 1 && ! $defaultAddress) {
                $defaultAddress = $address;
            }

            if ($address->hasGhnLocation() && ! $firstGhnAddress) {
                $firstGhnAddress = $address;
            }
        }

        if (! $defaultAddress) {
            $defaultAddress = $firstAddress;
        }

        if ($defaultAddress && $defaultAddress->hasGhnLocation()) {
            return $defaultAddress;
        }

        if ($firstGhnAddress) {
            return $firstGhnAddress;
        }

        return $defaultAddress;
    }

    // Tìm địa chỉ của user theo id, nếu không có thì lấy địa chỉ mặc định.
    private function findUserAddress($userId, $addressId = null)
    {
        if (! $userId) {
            return null;
        }

        if ($addressId) {
            $address = ShippingAddress::where('id', $addressId)
                ->where('user_id', $userId)
                ->first();

            if ($address) {
                return $address;
            }
        }

        $address = ShippingAddress::where('user_id', $userId)->where('default', 1)->first();
        if ($address) {
            return $address;
        }

        return ShippingAddress::where('user_id', $userId)->first();
    }

    // Lấy mã giảm giá đang lưu trong session và kiểm tra còn dùng được không.
    private function sessionCoupon()
    {
        if (! Auth::check()) {
            return null;
        }

        $couponId = session('checkout_coupon.id');
        $coupon = $couponId ? Coupon::find($couponId) : null;
        $subtotal = $this->cartSubtotal($this->cart->items());

        if (! $coupon || $coupon->validateForUser(Auth::id(), $subtotal)) {
            session()->forget(['checkout_coupon', 'checkout_shipping_quote']);

            return null;
        }

        return $coupon;
    }

    // Khóa mã giảm giá lúc đặt hàng để tránh dùng quá số lượt.
    private function lockedSessionCoupon($subtotal)
    {
        if (! Auth::check()) {
            return null;
        }

        $couponId = session('checkout_coupon.id');
        if (! $couponId) {
            return null;
        }

        $coupon = Coupon::whereKey($couponId)->lockForUpdate()->first();
        if (! $coupon) {
            session()->forget(['checkout_coupon', 'checkout_shipping_quote']);
            throw new Exception('Mã giảm giá không còn hiệu lực.');
        }

        if ($error = $coupon->validateForUser(Auth::id(), $subtotal)) {
            session()->forget(['checkout_coupon', 'checkout_shipping_quote']);
            throw new Exception($error);
        }

        return $coupon;
    }

    // Đánh dấu voucher của user là đã sử dụng.
    private function markClaimedCouponUsed($coupon)
    {
        if (! $coupon || ! Auth::check()) {
            return;
        }

        Auth::user()->coupons()->syncWithoutDetaching([
            $coupon->id => [
                'claimed_at' => now(),
                'used_at' => now(),
            ],
        ]);
    }

    // Ghép phường, quận, tỉnh thành chuỗi địa chỉ.
    private function buildCityText(Request $request)
    {
        $parts = [];

        if ($request->guest_ward_name) {
            $parts[] = $request->guest_ward_name;
        }

        if ($request->guest_district_name) {
            $parts[] = $request->guest_district_name;
        }

        if ($request->guest_province_name) {
            $parts[] = $request->guest_province_name;
        }

        if ($parts) {
            return implode(', ', $parts);
        }

        return 'Đang cập nhật';
    }

    // Cộng tổng tiền sản phẩm trong giỏ hàng.
    private function cartSubtotal($cartItems)
    {
        $subtotal = 0;

        foreach ($cartItems as $item) {
            if (is_object($item) && isset($item->subtotal)) {
                $subtotal += (float) $item->subtotal;
            }

            if (is_array($item) && isset($item['subtotal'])) {
                $subtotal += (float) $item['subtotal'];
            }
        }

        return $subtotal;
    }
}
