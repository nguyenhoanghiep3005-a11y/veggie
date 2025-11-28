<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;

class CategoryController extends Controller
{
    public function showFormAddCate()
    {
        return view('admin.pages.categories-add');
    }
    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

        ]);
        $imagePath = null;
        if ($request->hasFile("image")) {
            $imagePath = $request->file("image");
            $fileName = now()->timestamp . '_' . uniqid() . '.' . $imagePath->getClientOriginalExtension();
            $imagePath = $imagePath->storeAs('uploads/categories', $fileName, 'public');
        }
        Category::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('category-description'),
            'image' => $imagePath,
        ]);
        return redirect()->route('admin.categories.add')->with('success', 'Danh mục đã được thêm thành công!');
    }
    public function index(Request $request)
    {
        $categories = Category::all();
        return view('admin.pages.categories', compact('categories'));
    }
    public function updateCategory(Request $request)
    {
        try {
            $category = Category::findOrFail($request->category_id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục không tồn tại.'
                ], 404);
            }
            $category->name = $request->name;
            $category->description = $request->description;

            if ($request->hasFile("image")) {
                if ($category->image) {
                    // Xóa ảnh cũ nếu có
                    Storage::disk('public')->delete($category->image);
                }
                $imagePath = $request->file("image");
                $fileName = now()->timestamp . '_' . uniqid() . '.' . $imagePath->getClientOriginalExtension();
                $imagePath = $imagePath->storeAs('uploads/categories', $fileName, 'public');

                $category->image = $imagePath;
            }
            $category->save();
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật danh mục thành công.',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $category->image ? asset('storage/' . $category->image) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật danh mục.'
            ]);
        }
    }
}
