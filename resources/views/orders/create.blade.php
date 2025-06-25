<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tạo Đơn hàng Mới') }}
            </h2>
            <a href="{{ route('orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                Quay lại
            </a>
        </div>
    </x-slot>

    <!-- Thêm CSS cho Select2 với hình ảnh -->
    <style>
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-results__option img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 12px;
        }
        .select2-container--default .select2-selection__rendered {
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection__rendered img {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 8px;
        }
        .product-option {
            display: flex;
            align-items: center;
            width: 100%;
        }
        .product-option img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 12px;
        }
        .product-option .product-info {
            flex: 1;
        }
        .product-option .product-name {
            font-weight: 500;
            color: #374151;
        }
        .product-option .product-price {
            font-size: 12px;
            color: #6B7280;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3B82F6;
        }
        .product-image-placeholder {
            width: 40px;
            height: 40px;
            background-color: #F3F4F6;
            border-radius: 4px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image-placeholder::before {
            content: "📦";
            font-size: 16px;
        }
        .select2-container--default .select2-selection__rendered .product-image-placeholder {
            width: 30px;
            height: 30px;
        }
        .select2-container--default .select2-selection__rendered .product-image-placeholder::before {
            font-size: 12px;
        }
    </style>

    <!-- Thêm thư viện Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('orders.store') }}" method="POST" id="order-form">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700">Tên khách hàng <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_name') border-red-500 @enderror">
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mt-4">Số điện thoại <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_phone') border-red-500 @enderror">
                                @error('customer_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Phương thức thanh toán <span class="text-red-500">*</span></label>
                                <select name="payment_method" id="payment_method" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_method') border-red-500 @enderror">
                                    <option value="">Chọn phương thức thanh toán</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Thẻ tín dụng</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700">Địa chỉ giao hàng <span class="text-red-500">*</span></label>
                            <textarea name="shipping_address" id="shipping_address" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('shipping_address') border-red-500 @enderror">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái đơn hàng</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>Đã gửi hàng</option>
                                <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sản phẩm <span class="text-red-500">*</span></label>
                            <div id="order-items" class="space-y-2">
                                <div class="hidden md:grid grid-cols-12 gap-2 mb-2 font-semibold text-xs text-gray-500 px-2">
                                    <div class="col-span-5">Sản phẩm</div>
                                    <div class="col-span-2">Giá</div>
                                    <div class="col-span-2">Số lượng</div>
                                    <div class="col-span-2">Thành tiền</div>
                                    <div class="col-span-1"></div>
                                </div>
                                @php $oldItems = old('items', [[]]); @endphp
                                @foreach($oldItems as $i => $item)
                                    <div class="grid grid-cols-12 gap-2 order-item-row bg-white border border-gray-200 rounded-lg p-2 hover:shadow-md transition mb-2 items-center">
                                        <div class="col-span-12 md:col-span-5">
                                            <select name="items[{{ $i }}][product_id]" class="product-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                                <option value="">Chọn sản phẩm</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}" {{ (isset($item['product_id']) && $item['product_id'] == $product->id) ? 'selected' : '' }}>
                                                        {{ $product->name }} - {{ number_format($product->price) }} VNĐ
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-6 md:col-span-2 flex items-center justify-center">
                                            <span class="product-price">{{ isset($item['product_id']) ? number_format(optional($products->find($item['product_id']))->price) : '' }}</span>
                                        </div>
                                        <div class="col-span-6 md:col-span-2">
                                            <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" class="quantity-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        </div>
                                        <div class="col-span-6 md:col-span-2 flex items-center justify-center">
                                            <span class="item-total">0</span>
                                        </div>
                                        <div class="col-span-6 md:col-span-1 flex items-center justify-center">
                                            <button type="button" class="remove-item text-red-600 hover:text-red-900">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-item" class="mt-2 bg-green-500 hover:bg-green-600 text-black px-3 py-1 rounded text-xs font-medium">+ Thêm sản phẩm</button>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Ghi chú đơn hàng</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                            </div>
                            <div class="flex flex-col items-end justify-end">
                                <div class="text-lg font-semibold text-gray-700 mb-2">Tổng tiền: <span id="order-total" class="text-indigo-600">0</span> VNĐ</div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Hủy
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-black px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Tạo đơn hàng
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
        
        function initializeSelect2() {
            $('.product-select').select2({
                templateResult: formatProductOption,
                templateSelection: formatProductSelection,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        }
        
        function formatProductOption(product) {
            if (!product.id || product.id === '') {
                return product.text;
            }
            
            const productData = products[product.id];
            if (!productData) {
                return product.text;
            }
            
            const imageUrl = productData.image ? `/storage/${productData.image}` : '';
            const imageHtml = imageUrl ? `<img src="${imageUrl}" alt="${productData.name}" class="product-image" />` : '<div class="product-image-placeholder"></div>';
            
            return $(`
                <div class="product-option">
                    ${imageHtml}
                    <div class="product-info">
                        <div class="product-name">${productData.name}</div>
                        <div class="product-price">${formatNumber(productData.price)} VNĐ</div>
                    </div>
                </div>
            `);
        }
        
        function formatProductSelection(product) {
            if (!product.id || product.id === '') {
                return product.text;
            }
            
            const productData = products[product.id];
            if (!productData) {
                return product.text;
            }
            
            const imageUrl = productData.image ? `/storage/${productData.image}` : '';
            const imageHtml = imageUrl ? `<img src="${imageUrl}" alt="${productData.name}" class="product-image" />` : '<div class="product-image-placeholder"></div>';
            
            return $(`
                <div class="product-option">
                    ${imageHtml}
                    <div class="product-info">
                        <div class="product-name">${productData.name}</div>
                    </div>
                </div>
            `);
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
            // Khởi tạo Select2 cho các dropdown hiện có
            initializeSelect2();
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
                row.className = 'grid grid-cols-12 gap-2 order-item-row bg-white border border-gray-200 rounded-lg p-2 hover:shadow-md transition mb-2 items-center';
                
                const productOptions = Object.values(products).map(product => {
                    const imageUrl = product.image ? `/storage/${product.image}` : '';
                    return `<option value="${product.id}" data-price="${product.price}" data-image="${imageUrl}">${product.name} - ${formatNumber(product.price)} VNĐ</option>`;
                }).join('');
                
                row.innerHTML = `
                    <div class="col-span-12 md:col-span-5">
                        <select name="items[${index}][product_id]" class="product-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Chọn sản phẩm</option>
                            ${productOptions}
                        </select>
                    </div>
                    <div class="col-span-6 md:col-span-2 flex items-center justify-center">
                        <span class="product-price"></span>
                    </div>
                    <div class="col-span-6 md:col-span-2">
                        <input type="number" name="items[${index}][quantity]" value="1" min="1" class="quantity-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    </div>
                    <div class="col-span-6 md:col-span-2 flex items-center justify-center">
                        <span class="item-total">0</span>
                    </div>
                    <div class="col-span-6 md:col-span-1 flex items-center justify-center">
                        <button type="button" class="remove-item text-red-600 hover:text-red-900">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                document.getElementById('order-items').appendChild(row);
                
                // Khởi tạo Select2 cho dropdown mới
                $(row).find('.product-select').select2({
                    templateResult: formatProductOption,
                    templateSelection: formatProductSelection,
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
                
                updateTotals();
            });
            
            document.getElementById('order-items').addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    const row = e.target.closest('.order-item-row');
                    // Destroy Select2 trước khi xóa
                    $(row).find('.product-select').select2('destroy');
                    row.remove();
                    updateTotals();
                }
            });
        });
    </script>
</x-app-layout> 