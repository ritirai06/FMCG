@extends('layouts.app')

@section('title', 'Create Order')
@section('page_title', 'Create New Order')

@section('navbar_right')
  <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back to Orders
  </a>
@endsection

@section('content')

<!-- Error Messages -->
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top:20px;">
    <strong><i class="bi bi-exclamation-circle-fill me-2"></i>Validation Errors</strong>
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

    <!-- ORDER INFORMATION -->
    <div class="row">
        <!-- Store & Customer Info -->
        <div class="col-lg-6">
            <div class="filter-card" style="margin-top:0;">
                <h6 class="fw-bold mb-4" style="color:var(--primary);">
                    <i class="bi bi-info-circle me-2"></i>Order Information
                </h6>
                
                <!-- Store Selection -->
                <div class="mb-4">
                    <label for="store_id" class="form-label fw-600">Store <span class="text-danger">*</span></label>
                    <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required
                        style="border:1px solid rgba(59,130,246,.2);border-radius:10px;">
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
                <div class="mb-4">
                    <label for="customer_name" class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                           placeholder="Enter customer name" value="{{ old('customer_name') }}" required
                           style="border:1px solid rgba(59,130,246,.2);border-radius:10px;">
                    @error('customer_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Customer Phone -->
                <div class="mb-4">
                    <label for="customer_phone" class="form-label fw-600">Customer Phone <span class="text-danger">*</span></label>
                    <input type="tel" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" 
                           placeholder="Enter phone number" value="{{ old('customer_phone') }}" required
                           style="border:1px solid rgba(59,130,246,.2);border-radius:10px;">
                    @error('customer_phone')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Product Selection -->
        <div class="col-lg-6">
            <div class="filter-card" style="margin-top:0;">
                <h6 class="fw-bold mb-4" style="color:var(--primary);">
                    <i class="bi bi-bag-plus me-2"></i>Add Products
                </h6>

                <!-- Product Dropdown -->
                <div class="mb-4">
                    <label for="product_id" class="form-label fw-600">Select Product</label>
                    <select name="product_id" id="product_id" class="form-select"
                        style="border:1px solid rgba(59,130,246,.2);border-radius:10px;">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" data-stock="{{ $product->stock_quantity }}">
                            {{ $product->name }} (₹{{ number_format($product->sale_price, 2) }}) - Stock: {{ $product->stock_quantity }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity Input -->
                <div class="mb-4">
                    <label for="quantity" class="form-label fw-600">Quantity</label>
                    <input type="number" id="quantity" class="form-control" placeholder="Enter quantity" value="1" min="1"
                        style="border:1px solid rgba(59,130,246,.2);border-radius:10px;">
                </div>

                <!-- Add to Cart Button -->
                <button type="button" class="btn btn-gradient w-100" onclick="addToCart()">
                    <i class="bi bi-plus-circle me-1"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>

    <!-- CART TABLE -->
    <div class="table-card">
        <h6 class="fw-bold mb-4" style="color:var(--primary);">
            <i class="bi bi-cart3 me-2"></i>Order Items
        </h6>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr style="border-bottom:2px solid rgba(59,130,246,.2);">
                        <th style="font-weight:700;color:var(--primary);">Product</th>
                        <th style="font-weight:700;color:var(--primary);">Unit Price</th>
                        <th style="font-weight:700;color:var(--primary);">Qty</th>
                        <th style="font-weight:700;color:var(--primary);">Subtotal</th>
                        <th style="font-weight:700;color:var(--primary);">Action</th>
                    </tr>
                </thead>
                <tbody id="cartBody">
                    <tr id="emptyCart" class="text-center" style="color:var(--text-muted);">
                        <td colspan="5" class="py-5">
                            <i class="bi bi-cart-x" style="font-size:28px;opacity:0.5;margin-bottom:10px;display:block;"></i>
                            No items added yet
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div style="text-align:right;padding:20px;border-top:1px solid rgba(0,0,0,.05);">
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:15px;background:rgba(99,102,241,.08);border-radius:10px;">
                        <strong style="font-weight:700;color:var(--primary);">Total Amount:</strong>
                        <strong id="totalAmount" style="font-size:20px;color:var(--accent);">₹0.00</strong>
                    </div>
                    <input type="hidden" id="itemsInput" name="items">
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div style="margin-top:30px;display:flex;gap:10px;justify-content:flex-end;">
        <a href="{{ route('orders.index') }}" class="btn btn-light" style="border:1px solid rgba(59,130,246,.2);">
            <i class="bi bi-x-lg me-1"></i>Cancel
        </a>
        <button type="submit" class="btn btn-gradient">
            <i class="bi bi-check-circle me-1"></i>Create Order
        </button>
    </div>
</form>

<style>
.fw-600{font-weight:600;}
</style>

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
        cartBody.innerHTML = `<tr id="emptyCart" class="text-center" style="color:var(--text-muted);">
                                <td colspan="5" class="py-5">
                                    <i class="bi bi-cart-x" style="font-size:28px;opacity:0.5;margin-bottom:10px;display:block;"></i>
                                    No items added yet
                                </td>
                            </tr>`;
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
            <tr style="border-bottom:1px solid rgba(0,0,0,.05);">
                <td>
                    <strong>${item.product_name}</strong>
                </td>
                <td>₹${item.price.toFixed(2)}</td>
                <td>
                    <span class="badge bg-light" style="color:var(--primary);font-weight:700;">${item.quantity}</span>
                </td>
                <td><strong style="color:var(--primary);">₹${subtotal.toFixed(2)}</strong></td>
                <td>
                    <button type="button" class="btn btn-light btn-sm" onclick="removeFromCart(${index})" title="Remove">
                        <i class="bi bi-trash text-danger"></i>
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
