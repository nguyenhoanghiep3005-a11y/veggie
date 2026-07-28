<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use RuntimeException;

class CartController extends Controller
{
    private $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $quantity = (int) ($data['quantity'] ?? 1);

        try {
            $this->cart->add($product, $quantity);
        } catch (RuntimeException $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
            'cart_count' => $this->cart->count(),
        ]);
    }

    public function miniCart()
    {
        $cartItems = $this->cart->items();
        $cartTotal = $this->cart->total();

        return response()->json([
            'status' => true,
            'html' => view('clients.components.modals.includes.mini_cart', compact('cartItems', 'cartTotal'))->render(),
            'cart_count' => $this->cart->count(),
            'total' => number_format($cartTotal, 0, ',', '.'),
        ]);
    }

    public function removeMiniCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $this->cart->remove((int) $data['product_id']);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'cart_count' => $this->cart->count(),
            'total' => number_format($this->cart->total(), 0, ',', '.'),
        ]);
    }

    public function viewCart()
    {
        $cartItems = [];
        $cartTotal = 0;

        foreach ($this->cart->items() as $item) {
            $cartItems[] = [
                'product_id' => $item->product_id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'stock' => $item->stock,
                'subtotal' => $item->subtotal,
                'image' => $item->image,
            ];

            $cartTotal += $item->subtotal;
        }

        return view('clients.pages.cart', compact('cartItems', 'cartTotal'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = (int) $data['product_id'];
        $quantity = (int) $data['quantity'];

        try {
            $this->cart->update($productId, $quantity);
        } catch (RuntimeException $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 422);
        }

        $cartItem = null;
        foreach ($this->cart->items() as $item) {
            if ((int) $item->product_id == $productId) {
                $cartItem = $item;
                break;
            }
        }

        return response()->json([
            'success' => true,
            'quantity' => $cartItem ? $cartItem->quantity : $quantity,
            'subtotal' => number_format($cartItem ? $cartItem->subtotal : 0, 0, ',', '.'),
            'total' => number_format($this->cart->total(), 0, ',', '.'),
            'cart_count' => $this->cart->count(),
        ]);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $this->cart->remove((int) $data['product_id']);

        return response()->json([
            'success' => true,
            'total' => number_format($this->cart->total(), 0, ',', '.'),
            'cart_count' => $this->cart->count(),
        ]);
    }
}
