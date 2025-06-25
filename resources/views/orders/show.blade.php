<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi tiết Đơn hàng') }}: #{{ $order->order_code }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('orders.edit', $order) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Chỉnh sửa
                </a>
                <a href="{{ route('orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
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
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin đơn hàng</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Mã đơn hàng</dt>
                                    <dd class="mt-1 text-sm text-gray-900">#{{ $order->order_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ngày đặt</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                    <dd class="mt-1">
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
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tổng tiền</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ number_format($order->total_amount) }} VNĐ</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Ghi chú</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $order->notes ?: 'Không có ghi chú' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin khách hàng</h3>
                            @if($order->customer)
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tên khách hàng</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->phone }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Địa chỉ</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->address ?: 'N/A' }}</dd>
                                    </div>
                                </dl>
                            @else
                                <p class="text-sm text-gray-500">Không có thông tin khách hàng</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Danh sách sản phẩm</h3>
                        @if($order->orderItems && $order->orderItems->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($order->orderItems as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        @if($item->product && $item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                                                        @else
                                                            <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                                                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</div>
                                                            @if($item->product)
                                                                <div class="text-sm text-gray-500">{{ $item->product->category->name ?? 'N/A' }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ number_format($item->unit_price) }} VNĐ</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ number_format($item->total_price) }} VNĐ</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Tổng cộng:</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format($order->total_amount) }} VNĐ</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Không có sản phẩm nào</h3>
                                <p class="mt-1 text-sm text-gray-500">Đơn hàng này không có sản phẩm nào.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 