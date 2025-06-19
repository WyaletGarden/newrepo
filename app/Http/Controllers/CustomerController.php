<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

        // Tự động sinh mã khách hàng
        $data['customer_code'] = 'KH' . date('Ymd') . Str::random(4);

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Khách hàng đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $orders = $customer->orders()->with('orderItems.product')->orderBy('created_at', 'desc')->get();
        return view('customers.show', compact('customer', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Kiểm tra xem khách hàng có đơn hàng nào không
        if ($customer->orders()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Không thể xóa khách hàng đang có đơn hàng.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Khách hàng đã được xóa thành công.');
    }
}
