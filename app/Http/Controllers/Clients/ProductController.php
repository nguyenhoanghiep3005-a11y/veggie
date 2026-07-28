<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Hiển thị trang danh sách sản phẩm và phân trang.
    public function index(Request $request)
    {
        $categories = Category::with('products')->get();
        $selectedCategoryId = (int) $request->input('category_id', 0);
        $query = $this->baseProductQuery();

        if ($selectedCategoryId > 0) {
            $query->where('category_id', $selectedCategoryId);
        }

        $products = $query->paginate(9)->appends($request->only('category_id'));

        foreach ($products as $product) {
            $product->sold_quantity = $product->soldQuantity();
        }

        return view('clients.pages.products', compact('categories', 'products', 'selectedCategoryId'));
    }

    // Lọc sản phẩm theo danh mục, giá, sắp xếp và trả HTML cho Ajax.
    public function filter(Request $request)
    {
        $query = $this->baseProductQuery();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('minPrice') && $request->filled('maxPrice')) {
            $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);
        }

        switch ($request->input('sort_by')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(9);

        foreach ($products as $product) {
            $product->sold_quantity = $product->soldQuantity();
        }

        return response()->json([
            'products' => view('clients.components.products_grid', compact('products'))->render(),
            'pagination' => $products->links('clients.components.pagination.pagination_custom')->toHtml(),
        ]);
    }

    // Hiển thị chi tiết sản phẩm, biến thể và sản phẩm tương tự.
    public function detail($slug)
    {
        $product = $this->detailProduct($slug);
        $product->sold_quantity = $product->soldQuantity();
        $variantProducts = $this->productVariants($product);
        $relatedProducts = $this->baseProductQuery()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(6)
            ->get();

        foreach ($relatedProducts as $relatedProduct) {
            $relatedProduct->sold_quantity = $relatedProduct->soldQuantity();
        }

        return view('clients.pages.product-detail', compact('product', 'variantProducts', 'relatedProducts'));
    }

    // Lấy thông tin biến thể sản phẩm khi khách bấm đổi đơn vị.
    public function variant($slug)
    {
        $product = $this->detailProduct($slug);
        $product->sold_quantity = $product->soldQuantity();
        $variantProducts = $this->productVariants($product);
        $variants = [];

        foreach ($variantProducts as $variant) {
            $variant->sold_quantity = $variant->soldQuantity();
            $variantStock = $variant->sellableStock();
            $stockText = 'Hết hàng';

            if ($variantStock > 0) {
                $stockText = 'Còn '.$variantStock;
            }

            $variants[] = [
                'id' => $variant->id,
                'slug' => $variant->slug,
                'url' => route('product.detail', $variant->slug),
                'ajax_url' => route('product.variant', $variant->slug),
                'label' => $variant->variant_label,
                'stock' => $variantStock,
                'stock_text' => $stockText,
                'is_current' => $variant->id == $product->id,
            ];
        }

        $avgRating = $product->reviews->avg('rating');
        if (! $avgRating) {
            $avgRating = 0;
        }

        $categoryName = 'Đang cập nhật';
        if ($product->category) {
            $categoryName = $product->category->name;
        }

        return response()->json([
            'id' => $product->id,
            'slug' => $product->slug,
            'url' => route('product.detail', $product->slug),
            'name' => $product->base_name,
            'display_name' => $product->display_name,
            'price' => number_format($product->current_price, 0, ',', '.'),
            'old_price' => number_format($product->price, 0, ',', '.'),
            'is_on_sale' => $product->current_price < $product->price,
            'avg_rating' => number_format($avgRating, 1),
            'total_reviews' => $product->reviews->count(),
            'sold_quantity' => $product->sold_quantity,
            'stock' => $product->sellableStock(),
            'can_buy' => $product->sellableStock() > 0,
            'image_url' => $product->image_url,
            'images' => $product->detail_image_urls,
            'variant_label' => $product->variant_label,
            'description_text' => $product->description_text,
            'origin_text' => $product->origin_text,
            'storage_text' => $product->storage_text,
            'brand_text' => $product->brand_text,
            'manufacture_text' => $product->manufacture_text,
            'category_name' => $categoryName,
            'variants' => $variants,
            'review_html' => view('clients.components.modals.includes.review-list', compact('product'))->render(),
            'modal_html' => view('clients.components.modals.includes.include-modals', compact('product'))->render(),
        ]);
    }

    // Query chung cho sản phẩm client: còn bán và còn hàng.
    private function baseProductQuery()
    {
        return Product::with('firstImage', 'images', 'reviews', 'orderItems.order')
            ->where('status', 'int_stock')
            ->where('stock', '>', 0);
    }

    // Lấy sản phẩm chi tiết và chuẩn bị thông tin mô tả.
    private function detailProduct($slug)
    {
        $product = Product::with(['category', 'images', 'firstImage', 'reviews.user', 'orderItems.order'])
            ->where('slug', $slug)
            ->where('status', 'int_stock')
            ->firstOrFail();

        $descriptionInfo = $this->productDescriptionInfo($product);
        $product->description_text = $descriptionInfo['description'];
        $product->storage_text = $descriptionInfo['storage'];
        $product->brand_text = $descriptionInfo['brand'];
        $product->manufacture_text = $descriptionInfo['manufacture'];
        $product->origin_text = $descriptionInfo['origin'];
        $product->detail_image_urls = $this->productImageUrls($product);

        return $product;
    }

    // Tìm các sản phẩm cùng tên gốc để làm nút chọn đơn vị.
    private function productVariants($product)
    {
        $baseName = mb_strtolower($product->base_name, 'UTF-8');
        $products = Product::with('firstImage', 'images', 'reviews', 'orderItems.order')
            ->where('category_id', $product->category_id)
            ->where('status', 'int_stock')
            ->get();

        $variantProducts = [];

        foreach ($products as $variant) {
            if (mb_strtolower($variant->base_name, 'UTF-8') == $baseName) {
                $variant->variant_sort = $this->variantSortValue($variant->variant_label);
                $variant->is_current_variant = $variant->id == $product->id;
                $variantProducts[] = $variant;
            }
        }

        usort($variantProducts, function ($firstVariant, $secondVariant) {
            if ($firstVariant->variant_sort == $secondVariant->variant_sort) {
                return 0;
            }

            if ($firstVariant->variant_sort < $secondVariant->variant_sort) {
                return -1;
            }

            return 1;
        });

        return $variantProducts;
    }

    // Lấy danh sách ảnh chi tiết của sản phẩm.
    private function productImageUrls($product)
    {
        $imageUrls = [];

        if ($product->relationLoaded('images') && $product->images->isNotEmpty()) {
            foreach ($product->images as $image) {
                $imageUrls[] = $image->image_url;
            }
        }

        if (count($imageUrls) > 0) {
            return $imageUrls;
        }

        return [asset('storage/uploads/products/default.png')];
    }

    // Quy đổi đơn vị g/kg thành số để sắp xếp biến thể.
    private function variantSortValue($label)
    {
        $label = str_replace(',', '.', mb_strtolower($label, 'UTF-8'));

        if (! preg_match('/([\d.]+)\s*(g|gram|kg)/u', $label, $matches)) {
            return 999999;
        }

        $value = (float) $matches[1];

        if ($matches[2] == 'kg') {
            return $value * 1000;
        }

        return $value;
    }

    // Tách mô tả sản phẩm theo từng dòng nhập trong form admin.
    private function productDescriptionInfo($product)
    {
        $lines = $this->descriptionLines($product);

        $info = [
            'description' => 'Đang cập nhật',
            'storage' => 'Đang cập nhật',
            'brand' => 'Đang cập nhật',
            'manufacture' => 'Đang cập nhật',
            'origin' => 'Đang cập nhật',
        ];

        $normalLines = [];

        foreach ($lines as $index => $line) {
            $labeled = $this->labeledDescriptionLine($line);

            if ($labeled['label'] == 'Bảo quản') {
                $info['storage'] = $labeled['value'];
                continue;
            }

            if ($labeled['label'] == 'Thương hiệu') {
                $info['brand'] = $labeled['value'];
                continue;
            }

            if ($labeled['label'] == 'Sản xuất') {
                $info['manufacture'] = $labeled['value'];
                $info['origin'] = $labeled['value'];
                continue;
            }

            if ($labeled['label'] == 'Nguồn gốc') {
                $info['origin'] = $labeled['value'];
                continue;
            }

            $normalLines[] = $line;
        }

        if (count($normalLines) > 0) {
            $info['description'] = $normalLines[0];
        }

        if (isset($normalLines[1]) && $info['storage'] == 'Đang cập nhật') {
            $info['storage'] = $normalLines[1];
        }

        if (isset($normalLines[2]) && $info['brand'] == 'Đang cập nhật') {
            $info['brand'] = $normalLines[2];
        }

        if (isset($normalLines[3]) && $info['manufacture'] == 'Đang cập nhật') {
            $info['manufacture'] = $normalLines[3];
        }

        if ($info['origin'] == 'Đang cập nhật' && $info['manufacture'] != 'Đang cập nhật') {
            $info['origin'] = $info['manufacture'];
        }

        return $info;
    }

    // Lấy các dòng mô tả không rỗng từ textarea sản phẩm.
    private function descriptionLines($product)
    {
        $result = [];
        $lines = preg_split('/\r\n|\r|\n/', (string) $product->description);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line != '') {
                $result[] = $line;
            }
        }

        return $result;
    }

    // Nhận diện dòng có nhãn như Bảo quản:, Thương hiệu:, Sản xuất:.
    private function labeledDescriptionLine($line)
    {
        $labels = ['Nguồn gốc', 'Bảo quản', 'Thương hiệu', 'Sản xuất'];

        foreach ($labels as $label) {
            $prefix = $label.':';

            if (mb_stripos($line, $prefix, 0, 'UTF-8') === 0) {
                return [
                    'label' => $label,
                    'value' => trim(mb_substr($line, mb_strlen($prefix, 'UTF-8'), null, 'UTF-8')),
                ];
            }
        }

        return [
            'label' => '',
            'value' => $line,
        ];
    }

}
