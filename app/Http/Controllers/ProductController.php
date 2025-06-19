<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo tồn kho
        if ($request->filled('stock')) {
            if ($request->stock === 'in_stock') {
                $query->inStock();
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock_quantity', 0);
            }
        }

        // Lọc theo giá
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::active()->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'product_code' => 'nullable|string|max:50|unique:products',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Tự động sinh mã sản phẩm nếu không nhập
        if (empty($data['product_code'])) {
            $data['product_code'] = 'SP' . date('Ymd') . Str::random(4);
        }

        // Upload hình ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $data['image'] = 'products/' . $imageName;
        }

        try {
            Product::create($data);
            return redirect()->route('products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo sản phẩm: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'product_code' => 'nullable|string|max:50|unique:products,product_code,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Upload hình ảnh mới
        if ($request->hasFile('image')) {
            // Xóa hình ảnh cũ
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $data['image'] = 'products/' . $imageName;
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Kiểm tra xem sản phẩm có trong đơn hàng chưa hoàn thành không
        $pendingOrders = $product->orderItems()
            ->whereHas('order', function($query) {
                $query->whereIn('status', ['pending', 'processing']);
            })->count();

        if ($pendingOrders > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Không thể xóa sản phẩm đang có trong đơn hàng chưa hoàn thành.');
        }

        // Xóa hình ảnh
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công.');
    }
}
