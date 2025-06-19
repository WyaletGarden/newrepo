<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi tiết Khách hàng') }}: {{ $customer->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('customers.edit', $customer) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Chỉnh sửa
                </a>
                <a href="{{ route('customers.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin khách hàng</h3>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tên khách hàng</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customer->name }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customer->email }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customer->phone }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ngày sinh</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customer->birth_date ? $customer->birth_date->format('d/m/Y') : 'N/A' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Giới tính</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @switch($customer->gender)
                                            @case('male')
                                                Nam
                                                @break
                                            @case('female')
                                                Nữ
                                                @break
                                            @case('other')
                                                Khác
                                                @break
                                            @default
                                                N/A
                                        @endswitch
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Loại khách hàng</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @switch($customer->customer_type)
                                            @case('regular')
                                                Khách hàng thường
                                                @break
                                            @case('vip')
                                                Khách hàng VIP
                                                @break
                                            @case('wholesale')
                                                Khách hàng sỉ
                                                @break
                                            @default
                                                N/A
                                        @endswitch
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $customer->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customer->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Địa chỉ</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customer->address ?: 'N/A' }}</dd>
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Ghi chú</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customer->notes ?: 'Không có ghi chú' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê đơn hàng</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Tổng số đơn hàng:</span>
                                    <span class="text-lg font-bold text-indigo-600">{{ $customer->orders_count ?? 0 }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Tổng chi tiêu:</span>
                                    <span class="text-lg font-bold text-green-600">{{ number_format($customer->total_spent ?? 0) }} VNĐ</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Đơn hàng gần nhất:</span>
                                    <span class="text-sm text-gray-600">
                                        {{ $customer->last_order_date ? $customer->last_order_date->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Giá trị đơn hàng TB:</span>
                                    <span class="text-sm text-gray-600">
                                        {{ $customer->orders_count > 0 ? number_format(($customer->total_spent ?? 0) / $customer->orders_count) : 0 }} VNĐ
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order History -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Lịch sử đơn hàng</h3>
                        
                        @if($customer->orders && $customer->orders->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đặt</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số sản phẩm</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($customer->orders->take(10) as $order)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                        #{{ $order->order_number }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $order->orderItems->count() }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ number_format($order->total_amount) }} VNĐ</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @switch($order->status)
                                                            @case('pending')
                                                                bg-yellow-100 text-yellow-800
                                                                @break
                                                            @case('processing')
                                                                bg-blue-100 text-blue-800
                                                                @break
                                                            @case('shipped')
                                                                bg-purple-100 text-purple-800
                                                                @break
                                                            @case('delivered')
                                                                bg-green-100 text-green-800
                                                                @break
                                                            @case('cancelled')
                                                                bg-red-100 text-red-800
                                                                @break
                                                            @default
                                                                bg-gray-100 text-gray-800
                                                        @endswitch">
                                                        @switch($order->status)
                                                            @case('pending')
                                                                Chờ xử lý
                                                                @break
                                                            @case('processing')
                                                                Đang xử lý
                                                                @break
                                                            @case('shipped')
                                                                Đã gửi hàng
                                                                @break
                                                            @case('delivered')
                                                                Đã giao hàng
                                                                @break
                                                            @case('cancelled')
                                                                Đã hủy
                                                                @break
                                                            @default
                                                                {{ $order->status }}
                                                        @endswitch
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Xem chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($customer->orders->count() > 10)
                                <div class="text-center mt-4">
                                    <p class="text-sm text-gray-500">Và {{ $customer->orders->count() - 10 }} đơn hàng khác...</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đơn hàng nào</h3>
                                <p class="mt-1 text-sm text-gray-500">Khách hàng này chưa có đơn hàng nào.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 