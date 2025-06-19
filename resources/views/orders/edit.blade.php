<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chỉnh sửa Đơn hàng') }}: #{{ $order->order_number }}
            </h2>
            <a href="{{ route('orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('orders.update', $order) }}" method="POST" id="order-form">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700">Khách hàng <span class="text-red-500">*</span></label>
                                <select name="customer_id" id="customer_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_id') border-red-500 @enderror">
                                    <option value="">Chọn khách hàng</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái đơn hàng</label>
                                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Đã gửi hàng</option>
                                    <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sản phẩm <span class="text-red-500">*</span></label>
                            <div id="order-items">
                                <div class="grid grid-cols-12 gap-2 mb-2 font-semibold text-xs text-gray-500">
                                    <div class="col-span-5">Sản phẩm</div>
                                    <div class="col-span-2">Giá</div>
                                    <div class="col-span-2">Số lượng</div>
                                    <div class="col-span-2">Thành tiền</div>
                                    <div class="col-span-1"></div>
                                </div>
                                @php 
                                    $oldItems = old('items', $order->orderItems->map(function($item) {
                                        return [
                                            'product_id' => $item->product_id,
                                            'quantity' => $item->quantity,
                                            'price' => $item->price
                                        ];
                                    })->toArray());
                                @endphp
                                @foreach($oldItems as $i => $item)
                                    <div class="grid grid-cols-12 gap-2 mb-2 order-item-row">
                                        <div class="col-span-5">
                                            <select name="items[{{ $i }}][product_id]" class="product-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                                <option value="">Chọn sản phẩm</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ (isset($item['product_id']) && $item['product_id'] == $product->id) ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-2 flex items-center">
                                            <span class="product-price">{{ isset($item['product_id']) ? number_format(optional($products->find($item['product_id']))->price) : '' }}</span>
                                        </div>
                                        <div class="col-span-2">
                                            <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" class="quantity-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        </div>
                                        <div class="col-span-2 flex items-center">
                                            <span class="item-total">0</span>
                                        </div>
                                        <div class="col-span-1 flex items-center">
                                            <button type="button" class="remove-item text-red-600 hover:text-red-900">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-item" class="mt-2 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-medium">+ Thêm sản phẩm</button>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Ghi chú đơn hàng</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes', $order->notes) }}</textarea>
                            </div>
                            <div class="flex flex-col items-end justify-end">
                                <div class="text-lg font-semibold text-gray-700 mb-2">Tổng tiền: <span id="order-total" class="text-indigo-600">0</span> VNĐ</div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Hủy
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Cập nhật đơn hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const products = @json($products->keyBy('id'));
        function formatNumber(num) {
            return num.toLocaleString('vi-VN');
        }
        function updateTotals() {
            let total = 0;
            document.querySelectorAll('#order-items .order-item-row').forEach(function(row) {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity-input');
                const priceSpan = row.querySelector('.product-price');
                const itemTotalSpan = row.querySelector('.item-total');
                let price = 0;
                if (productSelect.value && products[productSelect.value]) {
                    price = parseInt(products[productSelect.value].price);
                    priceSpan.textContent = formatNumber(price);
                } else {
                    priceSpan.textContent = '';
                }
                const quantity = parseInt(quantityInput.value) || 1;
                const itemTotal = price * quantity;
                itemTotalSpan.textContent = formatNumber(itemTotal);
                total += itemTotal;
            });
            document.getElementById('order-total').textContent = formatNumber(total);
        }
        document.addEventListener('DOMContentLoaded', function() {
            updateTotals();
            document.getElementById('order-items').addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity-input')) {
                    updateTotals();
                }
            });
            document.getElementById('order-items').addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    updateTotals();
                }
            });
            document.getElementById('add-item').addEventListener('click', function() {
                const index = document.querySelectorAll('#order-items .order-item-row').length;
                const row = document.createElement('div');
                row.className = 'grid grid-cols-12 gap-2 mb-2 order-item-row';
                row.innerHTML = `
                    <div class="col-span-5">
                        <select name="items[${index}][product_id]" class="product-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Chọn sản phẩm</option>
                            ${Object.values(products).map(product => `<option value="${product.id}" data-price="${product.price}">${product.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-span-2 flex items-center">
                        <span class="product-price"></span>
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="items[${index}][quantity]" value="1" min="1" class="quantity-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    </div>
                    <div class="col-span-2 flex items-center">
                        <span class="item-total">0</span>
                    </div>
                    <div class="col-span-1 flex items-center">
                        <button type="button" class="remove-item text-red-600 hover:text-red-900">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                document.getElementById('order-items').appendChild(row);
                updateTotals();
            });
            document.getElementById('order-items').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('.order-item-row').remove();
                    updateTotals();
                }
            });
        });
    </script>
</x-app-layout> 