<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function showFormAddProduct()
    {
        $categories = Category::all();
        return view('admin.pages.product-add', compact('categories'));
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        $slug = Str::slug($request->name) . '-' . time();

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'unit' => $request->unit,
            'status' => 'out_of_stock',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = 'uploads/products/' . $imageName;

                // Resize bằng Intervention


                Storage::disk('public')->put($path, file_get_contents($image));


                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        return redirect()->route('admin.product.add')
            ->with('success', 'Thêm sản phẩm thành công!');
    }
    public function index()
    {

        $products = Product::with('category', 'images', 'firstImage', 'inventories')
            ->orderByDesc('id')
            ->get();
        $categories = Category::all();
        return view('admin.pages.product', compact('products', 'categories'));
    }
    public function updateProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ]);
        $product = Product::find($request->product_id);
        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'unit' => $request->unit
        ]);
        // Cập nhật hình ảnh
        if ($request->hasFile('images')) {

            // Xóa ảnh cũ
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image);
            }
            ProductImage::where('product_id', $product->id)->delete();
            // Thêm ảnh mới
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = 'uploads/products/' . $imageName;
                Storage::disk('public')->put($path, file_get_contents($image));
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }
        $product->load('category', 'images', 'inventories');

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category_name' => optional($product->category)->name ?? 'Chưa phân loại',
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'unit' => $product->unit,
                'status' => $product->stock > 0 ? 'Còn hàng' : 'Hết hàng',
                'images' => $product->images->map(fn($img) => asset('storage/' . $img->image))
            ]
        ]);
    }
    public function deleteProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $product = Product::find($request->product_id);

        // Xóa ảnh sản phẩm khỏi storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        // Xóa bản ghi ảnh sản phẩm khỏi database
        ProductImage::where('product_id', $product->id)->delete();

        // Xóa sản phẩm
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa sản phẩm thành công!'
        ]);
    }
    public function confirmOrder(Request $request)
    {
        $order = Order::find($request->id);
        if($order)
        {
            $order->status = 'processing';
               $order->save();
               return response()->json([
                'status' => true,
                'message' => 'Xác nhận đơn hàng thành công!'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Đơn hàng không tồn tại!'
        ]);
    }
}
