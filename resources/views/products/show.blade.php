<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi tiết Sản phẩm') }}: {{ $product->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Chỉnh sửa
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
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
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin sản phẩm</h3>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tên sản phẩm</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Danh mục</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Giá</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ number_format($product->price) }} VNĐ</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Số lượng tồn kho</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->stock_quantity }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Mã SKU</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->sku ?: 'N/A' }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->is_active ? 'Còn hàng' : 'Hết hàng' }}
                                        </span>
                                    </dd>
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Mô tả</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $product->description ?: 'Không có mô tả' }}
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hình ảnh sản phẩm</h3>
                            
                            @if($product->image)
                                <div class="border rounded-lg p-4">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded">
                                </div>
                            @else
                                <div class="border rounded-lg p-8 text-center">
                                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Không có hình ảnh</h3>
                                    <p class="mt-1 text-sm text-gray-500">Sản phẩm này chưa có hình ảnh.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Order History -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Lịch sử đơn hàng</h3>
                        
                        @if($product->orderItems && $product->orderItems->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($product->orderItems->take(10) as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('orders.show', $item->order) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        #{{ $item->order->order_number }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $item->order->customer->name ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ number_format($item->price) }} VNĐ</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ number_format($item->quantity * $item->price) }} VNĐ</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $item->order->created_at->format('d/m/Y') }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($product->orderItems->count() > 10)
                                <div class="text-center mt-4">
                                    <p class="text-sm text-gray-500">Và {{ $product->orderItems->count() - 10 }} đơn hàng khác...</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đơn hàng nào</h3>
                                <p class="mt-1 text-sm text-gray-500">Sản phẩm này chưa được đặt hàng.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 