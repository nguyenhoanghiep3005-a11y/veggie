<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GhnShippingService;

use function Flasher\Toastr\Prime\toastr;

class CheckoutController extends Controller
{
    protected $shippingService;

    public function __construct(GhnShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('default', 1)->first();

        if ($addresses->isEmpty()) {
            toastr()->error('Vui lòng thêm địa chỉ giao hàng');
            return redirect()->route('account');
        }

        if (!$defaultAddress) {
            $defaultAddress = $addresses->first();
        }

        if (!$defaultAddress->hasGhnLocation()) {
            $defaultAddress = $addresses->first(function ($address) {
                return $address->hasGhnLocation();
            });
        }

        if (!$defaultAddress) {
            toastr()->error('Vui lÃ²ng thÃªm Ä‘á»‹a chá»‰ giao hÃ ng cÃ³ Ä‘áº§y Ä‘á»§ tá»‰nh, quáº­n, phÆ°á»ng GHN');
            return redirect()->route('account');
        }

        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            toastr()->error('Giá» hÃ ng trá»‘ng');
            return redirect()->route('cart.index');
        }

        $amounts = $this->calculateOrderAmounts($cartItems, $defaultAddress);
        $totalPrice = $amounts['total'];
        $shippingFee = $amounts['shipping_fee'];
        $subtotal = $amounts['subtotal'];

        return view('clients.pages.checkout', compact('addresses', 'defaultAddress', 'cartItems', 'totalPrice', 'shippingFee', 'subtotal'));
    }

    public function getAddress(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'integer', 'exists:shipping_addresses,id']
        ]);

        $address = ShippingAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ']);
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
            ]
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'integer', 'exists:shipping_addresses,id'],
            'payment_method' => ['required', 'in:cash,paypal'],
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $address = $this->findAddressForUser($user->id, $request->address_id);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống');
        }

        if (!$address) {
            toastr()->error('Không tìm thấy địa chỉ giao hàng');
            return redirect()->route('checkout');
        }

        if (!$address->hasGhnLocation()) {
            toastr()->error('Vui lòng chọn địa chỉ có đầy đủ tỉnh, quận, phường GHN');
            return redirect()->route('checkout');
        }

        $shippingFee = $this->shippingService->calculateFee($address, $cartItems);

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $address->id;
            $order->total_price = 0;
            $order->status = 'pending';
            $order->save();

            $itemsTotal = $this->createOrderItemsAndUpdateStock($order, $cartItems);
            $order->total_price = $itemsTotal + $shippingFee;
            $order->save();

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null,
            ]);

            CartItem::where('user_id', $user->id)->delete();
            DB::commit();

            toastr()->success('Đặt hàng thành công');
            return redirect()->route('account');
        } catch (\Exception $e) {
            Log::error('Lỗi đặt hàng ' . $e->getMessage());
            DB::rollBack();

            toastr()->error('Có lỗi xảy ra, vui lòng thử lại ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }

    public function shippingFee(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'integer', 'exists:shipping_addresses,id']
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Giỏ hàng trống',
            ], 422);
        }

        $address = $this->findAddressForUser($user->id, $request->address_id);

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy địa chỉ giao hàng.',
            ], 404);
        }

        if (!$address->hasGhnLocation()) {
            return response()->json([
                'status' => false,
                'message' => 'Địa chỉ giao hàng chưa có đầy đủ tỉnh, quận, phường GHN.',
            ], 422);
        }

        $amounts = $this->calculateOrderAmounts($cartItems, $address);

        return response()->json([
            'status' => true,
            'data' => [
                'subtotal' => $amounts['subtotal'],
                'shipping_fee' => $amounts['shipping_fee'],
                'total' => $amounts['total'],
            ]
        ]);
    }

    private function findAddressForUser(int $userId, $addressId)
    {
        if ($addressId) {
            $address = ShippingAddress::where('id', $addressId)
                ->where('user_id', $userId)
                ->first();

            if ($address) {
                return $address;
            }
        }

        return $this->getDefaultAddress($userId);
    }

    private function getDefaultAddress(int $userId)
    {
        $address = ShippingAddress::where('user_id', $userId)
            ->where('default', 1)
            ->first();

        if ($address) {
            return $address;
        }

        return ShippingAddress::where('user_id', $userId)->first();
    }

    public function placeOrderPayPal(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'integer', 'exists:shipping_addresses,id'],
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $address = $this->findAddressForUser($user->id, $request->address_id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa chỉ giao hàng',
            ], 400);
        }

        if (!$address->hasGhnLocation()) {
            return response()->json([
                'success' => false,
                'message' => 'Địa chỉ giao hàng chưa có đầy đủ tỉnh, quận, phường GHN',
            ], 422);
        }

        $shippingFee = $this->shippingService->calculateFee($address, $cartItems);

        DB::beginTransaction();

        try {
            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $address->id;
            $order->total_price = 0;
            $order->status = 'pending';
            $order->save();

            $itemsTotal = $this->createOrderItemsAndUpdateStock($order, $cartItems);
            $order->total_price = $itemsTotal + $shippingFee;
            $order->save();

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'paypal',
                'transaction_id' => $request->transactionID,
                'amount' => $order->total_price,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            CartItem::where('user_id', $user->id)->delete();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Lỗi đặt hàng ' . $e->getMessage());
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại ' . $e->getMessage(),
            ], 500);
        }
    }

    private function calculateCartTotal($cartItems)
    {
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            if ($item->product) {
                $totalPrice += $item->product->calculatePriceByQuantity($item->quantity);
            }
        }

        return $totalPrice;
    }

    private function calculateOrderAmounts($cartItems, ?ShippingAddress $address = null): array
    {
        $subtotal = $this->calculateCartTotal($cartItems);
        $shippingFee = 0.0;
        
        if ($subtotal > 0) {
            if ($address) {
                $shippingFee = $this->shippingService->calculateFee($address, $cartItems);
            } else {
                $shippingFee = (float) config('ghn.fallback_fee', 25000);
            }
        }

        $total = max($subtotal + $shippingFee, 0);

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $total,
        ];
    }

    private function createOrderItemsAndUpdateStock($order, $cartItems)
    {
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;

            if (!$product) {
                throw new \Exception('Sản phẩm không tồn tại');
            }

            $quantityNeed = $item->quantity;
            $inventories = $product->availableInventories()->lockForUpdate()->get();
            $availableQuantity = 0;

            foreach ($inventories as $inventory) {
                $availableQuantity += $inventory->quantity_remaining;
            }

            if ($availableQuantity < $quantityNeed) {
                throw new \Exception('Sản phẩm ' . $product->name . ' không đủ hàng trong kho');
            }

            foreach ($inventories as $inventory) {
                if ($quantityNeed <= 0) {
                    break;
                }

                if ($inventory->quantity_remaining <= 0) {
                    continue;
                }

                $quantitySell = $quantityNeed;
                if ($inventory->quantity_remaining < $quantitySell) {
                    $quantitySell = $inventory->quantity_remaining;
                }

                $price = $inventory->sellingPrice();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'inventory_id' => $inventory->id,
                    'quantity' => $quantitySell,
                    'price' => $price,
                ]);

                $inventory->quantity_remaining = $inventory->quantity_remaining - $quantitySell;
                $inventory->refreshCondition();
                $inventory->save();

                $totalPrice += $price * $quantitySell;
                $quantityNeed -= $quantitySell;
            }

            $this->updateProductStatus($product);
        }

        return $totalPrice;
    }

    private function updateProductStatus($product)
    {
        $availableQuantity = $product->availableInventories()->sum('quantity_remaining');

        if ($availableQuantity > 0) {
            $product->status = 'int_stock';
        } else {
            $product->status = 'out_of_stock';
        }

        $product->save();
    }
}
