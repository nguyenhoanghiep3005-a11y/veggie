<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use RuntimeException;

class CartService
{
    public function items(): Collection
    {
        $cart = $this->normalizedCart();
        $cartItems = [];

        if (empty($cart)) {
            return new Collection($cartItems);
        }

        $productIds = array_keys($cart);
        $products = Product::with('firstImage')->whereIn('id', $productIds)->get();

        foreach ($cart as $productId => $line) {
            $product = null;

            foreach ($products as $item) {
                if ((int) $item->id == (int) $productId) {
                    $product = $item;
                    break;
                }
            }

            if (! $product) {
                continue;
            }

            $quantity = (int) $line['quantity'];
            $stock = (int) $product->sellableStock();
            $subtotal = $product->calculatePriceByQuantity($quantity);
            $price = $quantity > 0 ? $subtotal / $quantity : 0;

            $cartItems[] = (object) [
                'product_id' => $product->id,
                'product' => $product,
                'name' => $product->display_name,
                'image' => $product->image_url,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'stock' => $stock,
            ];
        }

        return new Collection($cartItems);
    }

    public function count(): int
    {
        $totalQuantity = 0;
        $cart = $this->normalizedCart();

        foreach ($cart as $line) {
            $totalQuantity += (int) $line['quantity'];
        }

        return $totalQuantity;
    }

    public function total(): float
    {
        $total = 0;
        $cartItems = $this->items();

        foreach ($cartItems as $item) {
            $total += (float) $item->subtotal;
        }

        return $total;
    }

    public function add(Product $product, int $quantity): void
    {
        $cart = $this->normalizedCart();
        $productId = (string) $product->id;
        $oldQuantity = 0;

        if (isset($cart[$productId])) {
            $oldQuantity = (int) $cart[$productId]['quantity'];
        }

        $newQuantity = $oldQuantity + max(1, $quantity);

        if ($newQuantity > $product->sellableStock()) {
            throw new RuntimeException('Số lượng vượt quá tồn sản phẩm.');
        }

        $cart[$productId] = [
            'product_id' => $product->id,
            'quantity' => $newQuantity,
        ];

        session()->put('cart', $cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $product = Product::findOrFail($productId);
        $cart = $this->normalizedCart();
        $key = (string) $productId;

        if (! isset($cart[$key])) {
            throw new RuntimeException('Sản phẩm không tồn tại trong giỏ hàng.');
        }

        if ($quantity > $product->sellableStock()) {
            throw new RuntimeException('Số lượng vượt quá tồn sản phẩm.');
        }

        $cart[$key] = [
            'product_id' => $productId,
            'quantity' => max(1, $quantity),
        ];

        session()->put('cart', $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->normalizedCart();
        $key = (string) $productId;

        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        session()->put('cart', $cart);
    }

    public function clear(): void
    {
        session()->forget('cart');
    }

    private function normalizedCart(): array
    {
        $cart = session('cart', []);
        $normalized = [];

        foreach ((array) $cart as $key => $line) {
            if (is_array($line)) {
                $productId = (int) ($line['product_id'] ?? $key);
                $quantity = (int) ($line['quantity'] ?? 0);
            } else {
                $productId = (int) $key;
                $quantity = (int) $line;
            }

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $normalized[(string) $productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $normalized);

        return $normalized;
    }
}
