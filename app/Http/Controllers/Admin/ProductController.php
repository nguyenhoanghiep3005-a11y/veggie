<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Hiển thị form thêm sản phẩm.
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.pages.product-add', compact('categories'));
    }

    // Lưu sản phẩm mới, tồn kho ban đầu bằng 0.
    public function store(Request $request)
    {
        $this->normalizeImageFiles($request);

        $data = $request->validate($this->addRules(), $this->messages());
        $data['slug'] = Str::slug($data['name'].'-'.$data['unit']).'-'.time();
        $data['stock'] = 0;
        $data['status'] = 'out_of_stock';

        $product = Product::create($data);
        $this->saveImages($product, $request);

        return redirect()->route('admin.product.add')
            ->with('success', 'Thêm sản phẩm thành công. Tồn kho sẽ tăng khi nhập hàng.');
    }

    // Hiển thị danh sách sản phẩm admin.
    public function index()
    {
        $products = Product::with(['category', 'images', 'firstImage'])
            ->orderBy('id', 'desc')
            ->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.pages.product', compact('products', 'categories'));
    }

    // Cập nhật sản phẩm bằng Ajax.
    public function update(Request $request)
    {
        $this->normalizeImageFiles($request);

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ] + $this->updateRules(), $this->messages());

        $product = Product::findOrFail($data['product_id']);
        unset($data['product_id']);

        $product->fill($data);
        $product->refreshStatus();
        $product->save();

        if ($request->hasFile('images')) {
            $this->deleteImages($product);
            $this->saveImages($product, $request);
        }

        $product->load('category', 'images', 'firstImage');

        $categoryName = 'Chưa phân loại';
        if ($product->category) {
            $categoryName = $product->category->name;
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'display_name' => $product->display_name,
                'slug' => $product->slug,
                'category_name' => $categoryName,
                'description' => $product->description,
                'price' => $product->price,
                'unit' => $product->unit,
                'image_url' => $product->image_url,
                'images' => $this->imageUrls($product),
            ],
        ]);
    }

    // Xóa sản phẩm và xóa ảnh liên quan.
    public function destroy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::with('images')->findOrFail($request->product_id);
        $this->deleteImages($product);
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa sản phẩm thành công!',
        ]);
    }

    // Rule kiểm tra khi thêm sản phẩm.
    private function addRules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'images' => 'nullable|array',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,gif,webp',
        ];
    }

    // Rule kiểm tra khi sửa sản phẩm.
    private function updateRules()
    {
        return $this->addRules();
    }

    // Message lỗi riêng cho form sản phẩm.
    private function messages()
    {
        return [
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
        ];
    }

    // Bỏ input ảnh rỗng để validate không báo lỗi khi không chọn ảnh.
    private function normalizeImageFiles($request)
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $files = [];
        foreach ($request->file('images') as $file) {
            if ($file && $file->isValid()) {
                $files[] = $file;
            }
        }

        if (count($files) == 0) {
            $request->files->remove('images');
            return;
        }

        $request->files->set('images', $files);
    }

    // Lưu các ảnh sản phẩm vào storage và bảng product_images.
    private function saveImages($product, $request)
    {
        if (! $request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $image) {
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $path = 'uploads/products/'.$imageName;
            Storage::disk('public')->put($path, file_get_contents($image));

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
            ]);
        }
    }

    // Xóa ảnh cũ khỏi storage và bảng product_images.
    private function deleteImages($product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        ProductImage::where('product_id', $product->id)->delete();
    }

    // Lấy danh sách đường dẫn ảnh để trả về Ajax.
    private function imageUrls($product)
    {
        $images = [];

        foreach ($product->images as $image) {
            $images[] = asset('storage/'.$image->image);
        }

        return $images;
    }
}
