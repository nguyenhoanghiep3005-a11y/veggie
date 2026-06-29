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

use function Flasher\Toastr\Prime\toastr;

class CheckoutController extends Controller
{
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

        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $totalPrice = $this->calculateCartTotal($cartItems);

        return view('clients.pages.checkout', compact('addresses', 'defaultAddress', 'cartItems', 'totalPrice'));
    }

    public function getAddress(Request $request)
    {
        $address = ShippingAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ']);
        }

        return response()->json([
            'success' => true,
            'data' => $address
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
            return redirect()->route('cart')->with('error', 'Giỏ hàng trống');
        }

        if (!$address) {
            toastr()->error('Không tìm thấy địa chỉ giao hàng');
            return redirect()->route('checkout');
        }

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $address->id;
            $order->total_price = 0;
            $order->status = 'pending';
            $order->save();

            $totalPrice = $this->createOrderItemsAndUpdateStock($order, $cartItems);
            $order->total_price = $totalPrice + 25000;
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

    public function shippingFree(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'integer', 'exists:shipping_addresses,id']
        ]);

        $user = Auth::use();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return reponse()->json([
                'status' => false,
                'message' => 'Giỏ hàng trống',
            ], 422);
        }

        $address = $this->findAddressForUser($user->id, $request->address_id);

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy địa chỉ giao hàng.',
            ], 400);
        }
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

            $totalPrice = $this->createOrderItemsAndUpdateStock($order, $cartItems);
            $order->total_price = $totalPrice + 25000;
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
