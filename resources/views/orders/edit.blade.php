<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chỉnh sửa Đơn hàng') }}: #{{ $order->order_code }}
            </h2>
            <a href="{{ route('orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(!in_array($order->status, ['pending', 'processing', 'shipped', 'cancelled']))
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Cảnh báo
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Đơn hàng này đang ở trạng thái "{{ ucfirst($order->status) }}" và không thể chỉnh sửa. Chỉ có thể chỉnh sửa đơn hàng đang chờ xử lý, đang xử lý, đã gửi hàng hoặc đã hủy.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @php
                        $canEdit = in_array($order->status, ['pending', 'processing', 'shipped', 'cancelled']);
                    @endphp
                    <form action="{{ route('orders.update', $order) }}" method="POST" id="order-form">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700">Khách hàng <span class="text-red-500">*</span></label>
                                <select name="customer_id" id="customer_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_id') border-red-500 @enderror" {{ !$canEdit ? 'disabled' : '' }}>
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
                                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('status') border-red-500 @enderror" {{ !$canEdit ? 'disabled' : '' }}>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Đã gửi hàng</option>
                                    <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Phương thức thanh toán <span class="text-red-500">*</span></label>
                                <select name="payment_method" id="payment_method" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_method') border-red-500 @enderror" {{ !$canEdit ? 'disabled' : '' }}>
                                    <option value="">Chọn phương thức thanh toán</option>
                                    <option value="cash" {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                    <option value="bank_transfer" {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                                    <option value="credit_card" {{ old('payment_method', $order->payment_method) == 'credit_card' ? 'selected' : '' }}>Thẻ tín dụng</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700">Địa chỉ giao hàng <span class="text-red-500">*</span></label>
                                <textarea name="shipping_address" id="shipping_address" rows="3" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('shipping_address') border-red-500 @enderror" {{ !$canEdit ? 'disabled' : '' }}>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                                @error('shipping_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sản phẩm <span class="text-red-500">*</span></label>
                            @error('items')
                                <p class="mt-1 text-sm text-red-600 mb-2">{{ $message }}</p>
                            @enderror
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
                                            'unit_price' => $item->unit_price
                                        ];
                                    })->toArray());
                                @endphp
                                @foreach($oldItems as $i => $item)
                                    <div class="grid grid-cols-12 gap-2 mb-2 order-item-row">
                                        <div class="col-span-5">
                                            <select name="items[{{ $i }}][product_id]" class="product-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('items.'.$i.'.product_id') border-red-500 @enderror" {{ !$canEdit ? 'disabled' : '' }} required>
                                                <option value="">Chọn sản phẩm</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ (isset($item['product_id']) && $item['product_id'] == $product->id) ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$i.'.product_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-span-2 flex items-center">
                                            <span class="product-price">{{ isset($item['product_id']) && $products->find($item['product_id']) ? number_format($products->find($item['product_id'])->price) : '' }}</span>
                                        </div>
                                        <div class="col-span-2">
                                            <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" class="quantity-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('items.'.$i.'.quantity') border-red-500 @enderror" {{ !$canEdit ? 'disabled' : '' }} required>
                                            @error('items.'.$i.'.quantity')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-span-2 flex items-center">
                                            <span class="item-total">{{ isset($item['product_id']) && $products->find($item['product_id']) ? number_format($products->find($item['product_id'])->price * ($item['quantity'] ?? 1)) : '0' }}</span>
                                        </div>
                                        <div class="col-span-1 flex items-center">
                                            <button type="button" class="remove-item text-red-600 hover:text-red-900" {{ !$canEdit ? 'disabled' : '' }}>
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-item" class="mt-2 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-medium" {{ !$canEdit ? 'disabled' : '' }}>+ Thêm sản phẩm</button>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Ghi chú đơn hàng</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('notes') border-red-500 @enderror" {{ !$canEdit ? 'disabled' : '' }}>{{ old('notes', $order->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex flex-col items-end justify-end">
                                <div class="text-lg font-semibold text-gray-700 mb-2">Tổng tiền: <span id="order-total" class="text-indigo-600">{{ number_format($order->total_amount) }}</span> VNĐ</div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Hủy
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-black px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out" {{ !$canEdit ? 'disabled' : '' }}>
                                Cập nhật đơn hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        select:disabled, input:disabled, textarea:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f3f4f6;
        }
    </style>

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
            
            // Chỉ cho phép tương tác khi có thể chỉnh sửa
            if ({{ $canEdit ? 'true' : 'false' }}) {
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
            }
        });
    </script>
</x-app-layout> 