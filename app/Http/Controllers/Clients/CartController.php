<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Thêm sản phẩm vào giỏ hàng (DB nếu đăng nhập, Session nếu chưa)
     */
    public function add(Request $request)
    {
        $product_id = $request->product_id;
        $quantity   = $request->quantity ?? 1;

        // Nếu đã đăng nhập → lưu DB
        if (Auth::check()) {
            $user_id  = Auth::id();
            $cartItem = CartItem::where('user_id', $user_id)
                ->where('product_id', $product_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'user_id'    => $user_id,
                    'product_id' => $product_id,
                    'quantity'   => $quantity,
                ]);
            }

            $count = CartItem::where('user_id', $user_id)->sum('quantity');

            return response()->json([
                'success'    => true,
                'message'    => 'Thêm vào giỏ hàng thành công',
                'cart_count' => $count,
            ]);
        }

        // Nếu chưa login → lưu SESSION (chỉ lưu id + quantity)
        $cart = session()->get('cart', []);

        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] += $quantity;
        } else {
            $cart[$product_id] = [
                'product_id' => $product_id,
                'quantity'   => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success'    => true,
            'message'    => 'Thêm vào giỏ hàng thành công',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    /**
     * Tải Mini Cart (giống trước, không động tới)
     */
    public function loadMiniCart()
    {
        if (Auth::check()) {
            $cartItems = CartItem::with('product')
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $sessionCart = session('cart', []);
            $productIds  = array_keys($sessionCart);

            $products  = Product::whereIn('id', $productIds)->get();
            $cartItems = collect();

            foreach ($sessionCart as $productId => $item) {
                $product = $products->where('id', $productId)->first();
                if ($product) {
                    $cartItems->push((object)[
                        'product'  => $product,
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
        }

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json([
            'status' => true,
            'html'   => view(
                'clients.components.modals.includes.mini_cart',
                ['cartItems' => $cartItems, 'subtotal' => $subtotal]
            )->render(),
        ]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFormMiniCart(Request $request)
    {
        $product_id = $request->product_id;

        if (Auth::check()) {
            $user_id = Auth::id();

            CartItem::where('user_id', $user_id)
                ->where('product_id', $product_id)
                ->delete();

            $count = CartItem::where('user_id', $user_id)->sum('quantity');

            return response()->json([
                'success'    => true,
                'message'    => 'Xóa sản phẩm khỏi giỏ hàng thành công',
                'cart_count' => $count,
            ]);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product_id])) {
            unset($cart[$product_id]);
            session()->put('cart', $cart);
        }

        $cart_count = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success'    => true,
            'message'    => 'Xóa sản phẩm khỏi giỏ hàng thành công',
            'cart_count' => $cart_count,
        ]);
    }

    /**
     * Trang /cart
     */
    public function viewCart()
    {
        if (Auth::check()) {
            // Lấy giỏ từ DB và chuẩn hóa về array
            $cartItems = CartItem::where('user_id', Auth::id())
                ->with('product.images')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_id' => $item->product->id,
                        'name'       => $item->product->name,
                        'price'      => $item->product->price,
                        'quantity'   => $item->quantity,
                        'stock'      => $item->product->stock,
                        'image'      => optional($item->product->images->first())->image
                            ?? 'uploads/products/product_default.png',
                    ];
                })
                ->toArray();
        } else {
            // Lấy giỏ từ session, join với products để có name/price/stock/image
            $sessionCart = session()->get('cart', []);
            $productIds  = array_keys($sessionCart);
            $products    = Product::whereIn('id', $productIds)
                ->with('images')
                ->get()
                ->keyBy('id');

            $cartItems = [];

            foreach ($sessionCart as $productId => $item) {
                if (!isset($products[$productId])) {
                    continue;
                }

                $product = $products[$productId];

                $cartItems[] = [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'price'      => $product->price,
                    'quantity'   => $item['quantity'],
                    'stock'      => $product->stock,
                    'image'      => optional($product->images->first())->image
                        ?? 'uploads/products/product_default.png',
                ];
            }
        }

        return view('clients.pages.cart', compact('cartItems'));
    }

    /**
     * Cập nhật số lượng trong cart (AJAX)
     */
    public function updateCart(Request $request)
    {
        $productId = $request->product_id;
        $quantity  = (int)$request->quantity;

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }

        // check tồn kho
        if ($quantity > $product->stock) {
            return response()->json(['error' => 'Số lượng vượt quá tồn kho'], 400);
        }

        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();

            if (!$cartItem) {
                return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();
        } else {
            $cart = session()->get('cart', []);

            if (!isset($cart[$productId])) {
                return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
            }

            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        // Tính toán lại
        $subtotal   = $quantity * $product->price;
        $total      = $this->caculateCartTotal();
        $grandTotal = $total + 25000;

        return response()->json([
            'quantity'   => $quantity,
            'subtotal'   => number_format($subtotal, 0, ',', '.'),
            'total'      => number_format($total, 0, ',', '.'),
            'grandTotal' => number_format($grandTotal, 0, ',', '.'),
        ]);
    }

    /**
     * Tính tổng tiền cart (dùng lại cho cả login và guest)
     */
    protected function caculateCartTotal()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())
                ->with('product')
                ->get()
                ->sum(fn($item) => $item->quantity * $item->product->price);
        }

        // Guest: đọc session, join với products
        $cart       = session()->get('cart', []);
        $productIds = array_keys($cart);

        if (empty($productIds)) {
            return 0;
        }

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;
        foreach ($cart as $pid => $item) {
            if (!isset($products[$pid])) {
                continue;
            }
            $total += $item['quantity'] * $products[$pid]->price;
        }

        return $total;
    }
    //remove
      public function removeCartItem(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::find($productId);
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
        $total      = $this->caculateCartTotal();
        $grandTotal = $total + 25000;

        return response()->json([
            'total'      => number_format($total, 0, ',', '.'),
            'grandTotal' => number_format($grandTotal, 0, ',', '.'),
        ]);
    }

}
