<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::active()->inStock()->with('category')->get();
        
        return view('orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string',
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
                'notes' => $request->notes,
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

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Chỉ có thể chỉnh sửa đơn hàng đang chờ xử lý.');
        }

        $customers = Customer::orderBy('name')->get();
        $products = Product::active()->with('category')->get();
        $order->load(['orderItems.product']);

        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Chỉ có thể chỉnh sửa đơn hàng đang chờ xử lý.');
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $order->update($request->all());

        return redirect()->route('orders.index')
            ->with('success', 'Đơn hàng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Chỉ có thể xóa đơn hàng đang chờ xử lý.');
        }

        try {
            DB::beginTransaction();

            // Hoàn trả số lượng tồn kho
            foreach ($order->orderItems as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            // Xóa order items
            $order->orderItems()->delete();

            // Xóa đơn hàng
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Đơn hàng đã được xóa thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('orders.index')
                ->with('error', 'Có lỗi xảy ra khi xóa đơn hàng.');
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Nếu hủy đơn hàng, hoàn trả tồn kho
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            try {
                DB::beginTransaction();
                
                foreach ($order->orderItems as $item) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái.');
            }
        }

        $order->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật thành công.');
    }
}
