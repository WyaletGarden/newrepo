<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi tiết Danh mục') }}: {{ $category->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('categories.edit', $category) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Chỉnh sửa
                </a>
                <a href="{{ route('categories.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
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
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin danh mục</h3>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tên danh mục</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $category->name }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Thứ tự hiển thị</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $category->sort_order }}</dd>
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Mô tả</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $category->description ?: 'Không có mô tả' }}
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $category->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                        </span>
                                    </dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Sản phẩm trong danh mục</h3>
                            
                            @if($category->products && $category->products->count() > 0)
                                <div class="space-y-3">
                                    @foreach($category->products->take(5) as $product)
                                        <div class="border rounded-lg p-3">
                                            <div class="flex items-center space-x-3">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                                                    <p class="text-sm text-gray-500">{{ number_format($product->price) }} VNĐ</p>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs text-gray-500">Tồn kho: {{ $product->stock_quantity }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($category->products->count() > 5)
                                        <div class="text-center">
                                            <p class="text-sm text-gray-500">Và {{ $category->products->count() - 5 }} sản phẩm khác...</p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có sản phẩm nào</h3>
                                    <p class="mt-1 text-sm text-gray-500">Danh mục này chưa có sản phẩm nào.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 