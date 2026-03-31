@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Create New Order</h1>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-circle"></i> Validation Errors</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form id="orderForm" method="POST" action="{{ route('orders.store') }}" onsubmit="return validateForm()">
        @csrf
        <div class="row">
            <!-- Store & Customer Info -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Store Selection -->
                        <div class="mb-3">
                            <label for="store_id" class="form-label">Store <span class="text-danger">*</span></label>
                            <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required>
                                <option value="">-- Select Store --</option>
                                @foreach($stores as $store)
                                <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>
                                    {{ $store->store_name }} - {{ $store->phone }}
                                </option>
                                @endforeach
                            </select>
                            @error('store_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Customer Name -->
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                                   placeholder="Enter customer name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Customer Phone -->
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Customer Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" 
                                   placeholder="Enter phone number" value="{{ old('customer_phone') }}" required>
                            @error('customer_phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Add Products</h5>
                    </div>
                    <div class="card-body">
                        <!-- Product Dropdown -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Select Product</label>
                            <select name="product_id" id="product_id" class="form-select">
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" data-stock="{{ $product->stock_quantity }}">
                                    {{ $product->name }} (₹{{ number_format($product->sale_price, 2) }}) - Stock: {{ $product->stock_quantity }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity Input -->
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" id="quantity" class="form-control" placeholder="Enter quantity" value="1" min="1">
                        </div>

                        <!-- Add to Cart Button -->
                        <button type="button" class="btn btn-success w-100" onclick="addToCart()">
                            <i class="fas fa-plus-circle"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr id="emptyCart" class="text-center text-muted py-4">
                                    <td colspan="5">No items added yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Total Amount:</strong>
                                    <strong id="totalAmount" class="fs-5 text-success">₹0.00</strong>
                                </div>
                                <input type="hidden" id="itemsInput" name="items">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary float-end">
                    <i class="fas fa-check-circle"></i> Create Order
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const cart = [];

function addToCart() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    
    if (!productSelect.value) {
        alert('Please select a product');
        return;
    }
    
    const productId = productSelect.value;
    const quantity = parseInt(quantityInput.value);
    const price = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price);
    const stock = parseInt(productSelect.options[productSelect.selectedIndex].dataset.stock);
    const productName = productSelect.options[productSelect.selectedIndex].text.split('(')[0].trim();
    
    if (quantity <= 0) {
        alert('Please enter valid quantity');
        return;
    }
    
    if (quantity > stock) {
        alert(`Only ${stock} items available in stock`);
        return;
    }
    
    // Check if product already in cart
    const existing = cart.find(item => item.product_id === productId);
    if (existing) {
        if ((existing.quantity + quantity) > stock) {
            alert(`Only ${stock} items available. Already added: ${existing.quantity}`);
            return;
        }
        existing.quantity += quantity;
    } else {
        cart.push({ product_id: productId, product_name: productName, quantity, price });
    }
    
    renderCart();
    productSelect.value = '';
    quantityInput.value = '1';
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const cartBody = document.getElementById('cartBody');
    const itemsInput = document.getElementById('itemsInput');
    
    if (cart.length === 0) {
        cartBody.innerHTML = '<tr id="emptyCart" class="text-center text-muted py-4"><td colspan="5">No items added yet</td></tr>';
        itemsInput.value = '';
        document.getElementById('totalAmount').textContent = '₹0.00';
        return;
    }
    
    if (document.getElementById('emptyCart')) {
        document.getElementById('emptyCart').remove();
    }
    
    let totalAmount = 0;
    cartBody.innerHTML = cart.map((item, index) => {
        const subtotal = item.quantity * item.price;
        totalAmount += subtotal;
        return `
            <tr>
                <td>${item.product_name}</td>
                <td>₹${item.price.toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>₹${subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    document.getElementById('totalAmount').textContent = '₹' + totalAmount.toFixed(2);
    itemsInput.value = JSON.stringify(cart);
}

function validateForm() {
    if (cart.length === 0) {
        alert('Please add at least one item to the order');
        return false;
    }
    return true;
}
</script>
@endsection
