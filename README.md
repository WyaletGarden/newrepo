<h1 align="center">Project: Hệ thống Quản Lý Bán Hàng</h1>

## 👤 Thông Tin Cá Nhân  
- **Họ tên**: Nguyễn Đức Hùng
- **Mã sinh viên**: 22010204
- **Lớp**: CNTT-VJ1
- **Môn học**: Xây dựng web nâng cao (TH3)

## 📈 Mục đích dự án
- Xây dựng một hệ thống quản lý bán hàng toàn diện nhằm giúp doanh nghiệp quản lý hiệu quả các hoạt động kinh doanh từ sản phẩm, khách hàng, đơn hàng đến báo cáo doanh thu.
- Hỗ trợ chủ cửa hàng và nhân viên quản lý hiệu quả các danh mục như sản phẩm, khách hàng, đơn hàng, doanh thu và các hóa đơn thanh toán, từ đó tối ưu hóa hoạt động kinh doanh.
- Hệ thống không chỉ là nền tảng quản lý, mà còn là công cụ phân tích dữ liệu giúp đưa ra quyết định kinh doanh thông minh dựa trên các báo cáo và thống kê chi tiết.

## ⚙ Hệ thống sử dụng
- **Backend**: PHP (Laravel 10 framework)
- **Authentication**: Laravel Breeze
- **Database**: MySQL (Local/Aiven Cloud)
- **ORM**: Eloquent ORM (Hệ thống ORM giúp tương tác với CSDL)
- **Frontend**: Blade engine, Tailwind CSS, Alpine.js
- **Security**: Laravel Security (CSRF, XSS, SQL Injection protection)
- **Validation**: Laravel Form Request Validation
- **File Upload**: Laravel Storage System

## ⚙️ Sơ đồ chức năng

### Sơ đồ tổng quát hệ thống
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Authentication │    │   Product Mgmt  │    │   Order Mgmt    │
│   (Breeze)      │    │   (CRUD)        │    │   (CRUD)        │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   User Mgmt     │    │   Category Mgmt │    │   Customer Mgmt │
│   (Admin/Staff) │    │   (CRUD)        │    │   (CRUD)        │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Dashboard     │    │   Reports       │    │   Security      │
│   (Statistics)  │    │   (Analytics)   │    │   (CSRF/XSS)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Quy trình sử dụng hệ thống
```
1. Đăng nhập → 2. Dashboard → 3. Quản lý → 4. Báo cáo → 5. Đăng xuất
```

### Quản lý sản phẩm
```
Thêm sản phẩm → Upload ảnh → Phân loại → Cập nhật tồn kho → Xóa sản phẩm
```

### Quản lý tài khoản
```
Tạo tài khoản → Phân quyền → Cập nhật thông tin → Quản lý session → Xóa tài khoản
```

### Quản lý đơn hàng
```
Tạo đơn hàng → Chọn sản phẩm → Tính toán giá → Xác nhận → Giao hàng
```

## 📊 Sơ đồ tuần tự

### Đăng ký tài khoản
```
User → Form Register → Validation → Create User → Email Verification → Login
```

### Đăng nhập
```
User → Form Login → Authentication → Session → Redirect to Dashboard
```

### Thêm sản phẩm
```
Admin → Form Product → Validation → Upload Image → Save → Success Message
```

### Tạo đơn hàng
```
Staff → Select Customer → Add Products → Calculate Total → Confirm Order → Update Stock
```

### Cập nhật thông tin
```
User → Edit Form → Validation → Update Database → Success Message
```

## Sơ đồ khối hệ thống

```
┌─────────────────────────────────────────────────────────────┐
│                    Hệ thống Quản Lý Bán Hàng                │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │   Frontend  │  │  Backend    │  │  Database   │         │
│  │  (Blade)    │  │ (Laravel)   │  │   (MySQL)   │         │
│  └─────────────┘  └─────────────┘  └─────────────┘         │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │   Security  │  │ Validation  │  │   Reports   │         │
│  │ (CSRF/XSS)  │  │ (Form Req)  │  │ (Analytics) │         │
│  └─────────────┘  └─────────────┘  └─────────────┘         │
└─────────────────────────────────────────────────────────────┘
```

<h1>Một số code minh họa</h1>

## Model

#### Product Model 
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'name',
        'category_id',
        'description',
        'price',
        'stock_quantity',
        'image',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}
```

#### Customer Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'email',
        'birth_date',
        'gender',
        'address'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
    }
}
```

#### Order Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'total_amount',
        'payment_method',
        'shipping_address',
        'notes',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
```

#### User Model

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

#### Category Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

## Controller

#### Product Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::active()->get();

        return view('products.index', compact('products', 'categories'));
    }

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

        // Upload hình ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $data['image'] = 'products/' . $imageName;
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

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

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công.');
    }
}
```

#### Order Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card',
            'shipping_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Tạo đơn hàng
            $order = Order::create([
                'order_code' => 'DH' . date('Ymd') . Str::random(4),
                'customer_id' => $request->customer_id,
                'total_amount' => 0,
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'status' => 'pending'
            ]);

            $totalAmount = 0;

            // Thêm các sản phẩm vào đơn hàng
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ số lượng trong kho.");
                }

                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $item['quantity'];
                $totalAmount += $totalPrice;

                // Tạo order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ]);

                // Cập nhật số lượng tồn kho
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Cập nhật tổng tiền đơn hàng
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Đơn hàng đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.index')
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }
}
```

#### Customer Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['customer_code'] = 'KH' . date('Ymd') . Str::random(4);

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Khách hàng đã được tạo thành công.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Thông tin khách hàng đã được cập nhật thành công.');
    }
}
```

## View

### Blade Template với Security

```php
{{-- resources/views/products/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Thêm Sản Phẩm Mới</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf {{-- CSRF Protection --}}
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Giá</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

<h1> 🔒 Security Setup</h1>

### CSRF Protection
Sử dụng `@csrf` directive để bảo vệ chống tấn công giả mạo yêu cầu từ phía người dùng:

```php
<form method="POST" action="{{ route('products.store') }}">
    @csrf {{-- CSRF Token tự động --}}
    <!-- Form fields -->
</form>
```

### XSS Prevention
Laravel Blade tự động escape output để chống XSS:

```php
{{-- Tự động escape --}}
{{ $product->name }}

{{-- Không escape (chỉ khi cần thiết) --}}
{!! $product->description !!}
```

### SQL Injection Prevention
Sử dụng Eloquent ORM để chống SQL Injection:

```php
// An toàn - Eloquent ORM
$products = Product::where('category_id', $categoryId)->get();

// An toàn - Query Builder
$products = DB::table('products')
    ->where('category_id', $categoryId)
    ->get();
```

### Authentication & Authorization
Middleware bảo vệ routes:

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('orders', OrderController::class);
});
```

### Form Validation
Validation rules cho tất cả input:

```php
$validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:customers',
    'price' => 'required|numeric|min:0',
    'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);
```

<h1> 🔗 Link </h1>

## Github Repository
[Link GitHub Repository](https://github.com/WyaletGarden/newrepo)

## Demo Video
[Link Demo Video](https://drive.google.com/drive/folders/1Ci2O0RuxRzUemyzszu6oHDMrd1SlgH3l?usp=sharing)


<h1>License & Copy Rights</h1>

The Laravel framework is open-sourced software licensed under the <a href="https://opensource.org/licenses/MIT" rel="nofollow">MIT license.</a>

---

## 📝 Changelog

### Version 1.0.0 (Latest Update)
- ✅ Hoàn thành hệ thống quản lý bán hàng cơ bản
- ✅ Tích hợp Laravel Breeze cho authentication
- ✅ Implement CRUD operations cho tất cả entities
- ✅ Bảo mật với CSRF, XSS protection
- ✅ Responsive UI với Tailwind CSS
- ✅ Database migrations và seeders
- ✅ Báo cáo và thống kê dashboard

### Cập nhật gần đây
- 🔄 Cập nhật README với thông tin chi tiết
- 🔄 Thêm hướng dẫn cài đặt và sử dụng
- 🔄 Cải thiện documentation
