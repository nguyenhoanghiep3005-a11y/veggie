<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with('product.firstImage')
            ->where('user_id', Auth::id())
            ->whereHas('product')
            ->latest()
            ->get();

        return view('clients.pages.wishlist', compact('wishlist'));
    }

    public function store(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng danh sách yêu thích.',
            ], 401);
        }

        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $data['product_id'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã thêm sản phẩm vào danh sách yêu thích.',
        ]);
    }

    public function destroy(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng danh sách yêu thích.',
            ], 401);
        }

        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $data['product_id'])
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích.',
        ]);
    }
}