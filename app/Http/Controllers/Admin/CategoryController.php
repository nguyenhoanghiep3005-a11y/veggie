<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Hiển thị form thêm danh mục.
    public function create()
    {
        return view('admin.pages.categories-add');
    }

    // Lưu danh mục mới vào database.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $imagePath = $this->saveImage($request);

        Category::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.categories.add')->with('success', 'Danh mục đã được thêm thành công!');
    }

    // Hiển thị danh sách danh mục.
    public function index(Request $request)
    {
        $categories = Category::all();

        return view('admin.pages.categories', compact('categories'));
    }

    // Cập nhật thông tin danh mục.
    public function update(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::find($request->category_id);

        if (! $category) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục không tồn tại.',
            ], 404);
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;

        if ($request->hasFile('image')) {
            $this->deleteImage($category->image);
            $category->image = $this->saveImage($request);
        }

        $category->save();

        $imageUrl = null;
        if ($category->image) {
            $imageUrl = asset('storage/'.$category->image);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật danh mục thành công.',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image' => $imageUrl,
            ],
        ]);
    }

    // Xóa danh mục khỏi database.
    public function destroy(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $category = Category::find($request->category_id);

        if (! $category) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục không tồn tại.',
            ], 404);
        }

        $this->deleteImage($category->image);
        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa danh mục thành công.',
        ]);
    }

    // Lưu ảnh danh mục và trả về đường dẫn ảnh.
    private function saveImage($request)
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $image = $request->file('image');
        $fileName = now()->timestamp.'_'.uniqid().'.'.$image->getClientOriginalExtension();

        return $image->storeAs('uploads/categories', $fileName, 'public');
    }

    // Xóa ảnh danh mục cũ nếu có.
    private function deleteImage($imagePath)
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
