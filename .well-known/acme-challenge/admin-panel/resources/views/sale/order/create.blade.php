<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jobick: Job Admin Dashboard Bootstrap 5 Template + FrontEnd</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/png" href="{{ asset('sale_assets/images/favicon.png') }}">
    <link href="{{ asset('sale_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/css/jquery.localizationTool.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        a[href*="support.w3itexperts"],
        a[href*="envato.market"],
        .sidebar-right,
        .sidebar-right-trigger,
        .sidebar-close-trigger,
        .dlab-demo-panel,
        .dlab-demo-trigger,
        #DZ_THEME_PANEL,
        #DZScript {
            display: none !important;
            visibility: hidden !important;
        }

        /* Keep action buttons stable on hover in create-order page */
        .order-btn-stable.btn-primary:hover,
        .order-btn-stable.btn-primary:focus {
            color: #fff !important;
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .order-btn-stable.btn-danger:hover,
        .order-btn-stable.btn-danger:focus {
            color: #fff !important;
            background-color: #f72b50 !important;
            border-color: #f72b50 !important;
        }

        .order-btn-stable.btn-danger.light:hover,
        .order-btn-stable.btn-danger.light:focus {
            color: #f72b50 !important;
            background-color: #ffecef !important;
            border-color: #f72b50 !important;
        }
    </style>
</head>
<body>
@php
    $resolveImage = function ($path, $fallback) {
        $path = ltrim((string) $path, '/');
        if ($path === '') {
            return $fallback;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        if (file_exists(public_path('uploads/admin/' . $path))) {
            return asset('uploads/admin/' . $path);
        }
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        return asset($path);
    };

    $companyDisplayName = $companyName ?? 'SalePanel';
    $companyLogo = $resolveImage(
        $companySettings?->company_logo ?? $companySettings?->logo ?? $companySettings?->profile_image,
        asset('sale_assets/images/logo-full.png')
    );

    $salesPersonName = collect([
        $user?->name ?? null,
        $user?->full_name ?? null,
        $user?->username ?? null,
    ])->map(fn ($v) => trim((string) $v))->first(fn ($v) => $v !== '');
    if (!$salesPersonName) {
        $salesPersonName = trim((string) ($salesPerson?->name ?? ''));
    }
    if (!$salesPersonName && !empty($user?->email)) {
        $salesPersonName = trim((string) strstr($user->email, '@', true));
    }
    $salesPersonName = $salesPersonName ?: 'Sales User';
    $salesPersonStatus = $user?->status ?? $salesPerson?->status ?? 'Active';
    $salesProfileImage = $resolveImage(
        $user?->avatar_path ?? $user?->profile_image ?? $salesPerson?->avatar_path ?? $salesPerson?->profile_image ?? $companySettings?->profile_image,
        $companyLogo
    );

    $productsForJs = collect($products ?? [])->map(function ($product) {
        return [
            'id' => $product->id,
            'label' => ($product->name ?: $product->product_name),
            'price' => (float)($product->sale_price ?? $product->mrp ?? $product->price ?? 0),
            'stock' => (int)($product->stock_quantity ?? 0),
        ];
    })->values();
@endphp

<div id="main-wrapper">
    <div class="nav-header">
        <a href="/sale/dashboard" class="brand-logo">
            <img class="logo-abbr" src="{{ $companyLogo }}" alt="{{ $companyDisplayName }}" style="width:42px;height:42px;object-fit:cover;border-radius:10px;">
            <span class="brand-title" style="font-size:14px;font-weight:600;color:#464646;margin-left:8px;">{{ $companyDisplayName }}</span>
        </a>
        <div class="nav-control">
            <div class="hamburger">
                <span class="line"></span><span class="line"></span><span class="line"></span>
            </div>
        </div>
    </div>

    <div class="header">
        <div class="header-content">
            <nav class="navbar navbar-expand">
                <div class="collapse navbar-collapse justify-content-between">
                    <div class="header-left">
                        <div class="dashboard_bar">{{ $companyDisplayName }} Dashboard</div>
                        <div class="nav-item d-flex align-items-center">
                            <form action="javascript:void(0);">
                                <div class="input-group search-area">
                                    <input type="text" class="form-control" placeholder="Search">
                                    <span class="input-group-text"><button type="submit" class="btn"><i class="flaticon-381-search-2"></i></button></span>
                                </div>
                            </form>
                            <div class="plus-icon">
                                <a href="javascript:void(0);"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <ul class="navbar-nav header-right">
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"><g data-name="Layer 2" transform="translate(-2 -2)"><path d="M22.571,15.8V13.066a8.5,8.5,0,0,0-7.714-8.455V2.857a.857.857,0,0,0-1.714,0V4.611a8.5,8.5,0,0,0-7.714,8.455V15.8A4.293,4.293,0,0,0,2,20a2.574,2.574,0,0,0,2.571,2.571H9.8a4.286,4.286,0,0,0,8.4,0h5.23A2.574,2.574,0,0,0,26,20,4.293,4.293,0,0,0,22.571,15.8Z"></path></g></svg>
                                <span class="badge light text-white bg-primary rounded-circle">9</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <i class="fas fa-cog" style="font-size:20px;line-height:1;color:#2b3674;"></i>
                                <span class="badge light text-white bg-primary rounded-circle">5</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <img src="{{ $salesProfileImage }}" width="20" alt="{{ $salesPersonName }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="/sale/profile" class="dropdown-item ai-icon"><span class="ms-2">{{ $salesPersonName }}</span></a>
                                <a href="/sale/attendance" class="dropdown-item ai-icon"><span class="ms-2">{{ $salesPersonStatus }}</span></a>
                                <a href="/sale/logout" class="dropdown-item ai-icon" onclick="return confirm('Are you sure you want to logout?')"><span class="ms-2">Logout</span></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="dlabnav">
        <div class="dlabnav-scroll">
            <div class="dropdown header-profile2">
                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                    <div class="header-info2 d-flex align-items-center">
                        <img src="{{ $salesProfileImage }}" alt="">
                        <div class="d-flex align-items-center sidebar-info">
                            <div>
                                <span class="font-w400 d-block">{{ $salesPersonName }}</span>
                                <small class="text-end font-w400">{{ $salesPersonStatus }}</small>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </a>
            </div>
            <ul class="metismenu" id="menu">
                <li><a href="/sale/dashboard"><i class="flaticon-025-dashboard"></i><span class="nav-text">Dashboard</span></a></li>
                <li>
                    <a class="has-arrow" href="javascript:void(0);" aria-expanded="false"><i class="flaticon-381-user-7"></i><span class="nav-text">Orders</span></a>
                    <ul aria-expanded="false">
                        <li><a href="/sale/order/create">Create Order</a></li>
                        <li><a href="/sale/order/list">Order List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0);" aria-expanded="false"><i class="flaticon-093-waving"></i><span class="nav-text">Stores</span></a>
                    <ul aria-expanded="false">
                        <li><a href="/sale/store/create">Create Store</a></li>
                        <li><a href="/sale/store/list">Store List</a></li>
                    </ul>
                </li>
                <li><a href="/sale/attendance"><i class="flaticon-381-user-4"></i><span class="nav-text">Attendance</span></a></li>
                <li><a href="/sale/profile"><i class="flaticon-381-internet"></i><span class="nav-text">Profile</span></a></li>
            </ul>
            <div class="plus-box">
                <p class="fs-14 font-w600 mb-2">Let SalePanel simplify<br>your sales workflow</p>
                <p class="plus-box-p">Manage stores, orders, and reports in one place</p>
            </div>
            <div class="copyright">
                <p><strong>FMCG</strong> - Simplify your sales workflow &copy; <span class="current-year">2026</span></p>
                <p class="fs-12">Manage stores, orders, and reports in one place</p>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            <div class="d-flex align-items-center mb-4">
                <h3 class="mb-0 me-auto">Create Order</h3>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('sale.order.store') }}" id="orderForm">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Order ID<span class="text-danger scale5 ms-2">*</span></label>
                                        <input type="text" class="form-control" value="Auto-generated on save" readonly>
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Select Store<span class="text-danger scale5 ms-2">*</span></label>
                                        <select name="store_id" class="form-control @error('store_id') is-invalid @enderror" required>
                                            <option value="">-- Select a Store --</option>
                                            @forelse($stores as $store)
                                                <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>{{ $store->store_name }} @if($store->phone) ({{ $store->phone }}) @endif</option>
                                            @empty
                                                <option disabled>No stores available</option>
                                            @endforelse
                                        </select>
                                        @error('store_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Customer / Owner Name<span class="text-danger scale5 ms-2">*</span></label>
                                        <input type="text" name="customer" class="form-control @error('customer') is-invalid @enderror" placeholder="Customer name" value="{{ old('customer') }}">
                                        @error('customer') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Sales Person</label>
                                        <input type="text" class="form-control" value="{{ $salesPersonName }}" readonly>
                                        <small class="text-muted">Auto-assigned to your account</small>
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Order Amount (Auto Calculated)</label>
                                        <input type="number" id="orderAmount" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" step="0.01" min="0" value="{{ old('amount', 0) }}" readonly>
                                        @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Order Date<span class="text-danger scale5 ms-2">*</span></label>
                                        <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', now()->toDateString()) }}">
                                        @error('order_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" class="form-control" placeholder="Customer mobile number" value="{{ old('contact_number') }}">
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Alternate Contact</label>
                                        <input type="text" name="alternate_contact" class="form-control" placeholder="Alternate number" value="{{ old('alternate_contact') }}">
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control" placeholder="City" value="{{ old('city') }}">
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" class="form-control" placeholder="State" value="{{ old('state') }}">
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Area / Route</label>
                                        <input type="text" name="area" class="form-control" placeholder="Route / area" value="{{ old('area') }}">
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-4">
                                        <label class="form-label">Pincode</label>
                                        <input type="text" name="pincode" class="form-control" placeholder="Pincode" value="{{ old('pincode') }}">
                                    </div>
                                    <div class="col-xl-12 mb-4">
                                        <label class="form-label">Order Notes</label>
                                        <textarea name="notes" class="form-control" rows="4" placeholder="Order details / product notes">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="form-label mb-0">Order Items<span class="text-danger scale5 ms-2">*</span></label>
                                        <button type="button" id="addItemBtn" class="btn btn-sm btn-primary order-btn-stable">Add Item</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead>
                                                <tr>
                                                    <th style="min-width:260px;">Product</th>
                                                    <th style="width:120px;">Available</th>
                                                    <th style="width:120px;">Qty</th>
                                                    <th style="width:140px;">Unit Price</th>
                                                    <th style="width:140px;">Subtotal</th>
                                                    <th style="width:90px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="orderItemsBody">
                                                @php
                                                    $oldItems = old('items', [['product_id' => '', 'quantity' => 1]]);
                                                @endphp
                                                @foreach($oldItems as $idx => $item)
                                                    <tr class="order-item-row">
                                                        <td>
                                                            <select name="items[{{ $idx }}][product_id]" class="form-control product-select" required>
                                                                <option value="">-- Select Product --</option>
                                                                @foreach(($products ?? collect()) as $product)
                                                                    @php
                                                                        $productLabel = $product->name ?: $product->product_name;
                                                                        $unitPrice = (float)($product->sale_price ?? $product->mrp ?? $product->price ?? 0);
                                                                    @endphp
                                                                    <option value="{{ $product->id }}"
                                                                            data-price="{{ $unitPrice }}"
                                                                            data-stock="{{ (int)($product->stock_quantity ?? 0) }}"
                                                                            {{ (string)($item['product_id'] ?? '') === (string)$product->id ? 'selected' : '' }}>
                                                                        {{ $productLabel }} (Stock: {{ (int)($product->stock_quantity ?? 0) }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control item-stock" value="0" readonly></td>
                                                        <td><input type="number" min="1" name="items[{{ $idx }}][quantity]" class="form-control item-qty" value="{{ (int)($item['quantity'] ?? 1) }}" required></td>
                                                        <td><input type="number" class="form-control item-price" step="0.01" value="0" readonly></td>
                                                        <td><input type="number" class="form-control item-subtotal" step="0.01" value="0" readonly></td>
                                                        <td><button type="button" class="btn btn-sm btn-danger remove-item-btn order-btn-stable">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @error('items') <div class="text-danger">{{ $message }}</div> @enderror
                                    @error('items.*.product_id') <div class="text-danger">{{ $message }}</div> @enderror
                                    @error('items.*.quantity') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Order Status<span class="text-danger scale5 ms-2">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline"><input class="form-check-input @error('status') is-invalid @enderror" type="radio" name="status" value="Pending" id="status_pending" @checked(old('status', 'Pending') == 'Pending')><label class="form-check-label" for="status_pending">Pending</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input @error('status') is-invalid @enderror" type="radio" name="status" value="Processing" id="status_processing" @checked(old('status') == 'Processing')><label class="form-check-label" for="status_processing">Processing</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input @error('status') is-invalid @enderror" type="radio" name="status" value="Out for Delivery" id="status_delivery" @checked(old('status') == 'Out for Delivery')><label class="form-check-label" for="status_delivery">Out for Delivery</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input @error('status') is-invalid @enderror" type="radio" name="status" value="Delivered" id="status_delivered" @checked(old('status') == 'Delivered')><label class="form-check-label" for="status_delivered">Delivered</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input @error('status') is-invalid @enderror" type="radio" name="status" value="Cancelled" id="status_cancelled" @checked(old('status') == 'Cancelled')><label class="form-check-label" for="status_cancelled">Cancelled</label></div>
                                        @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="card-footer text-end" style="margin: -1rem -1rem -1rem -1rem; padding: 1rem; border-top: 1px solid #eee;">
                                    <a href="{{ route('sale.order.list') }}" class="btn btn-danger light me-3 order-btn-stable">Cancel</a>
                                    <button type="submit" class="btn btn-primary order-btn-stable"><i class="fas fa-save me-2"></i>Create Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; FMCG <span class="current-year">2026</span></p>
        </div>
    </div>
</div>

<script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/owl-carousel/owl.carousel.js') }}"></script>
<script src="{{ asset('sale_assets/js/jquery.localizationTool.js') }}"></script>
<script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
<script src="{{ asset('sale_assets/js/dlabnav-init.js') }}"></script>
<script>
    (function () {
        const itemsBody = document.getElementById('orderItemsBody');
        const addItemBtn = document.getElementById('addItemBtn');
        const orderAmount = document.getElementById('orderAmount');

        const products = @json($productsForJs);

        const productOptionsHtml = ['<option value="">-- Select Product --</option>']
            .concat(products.map((product) => {
                const safeLabel = `${product.label} (Stock: ${product.stock})`;
                return `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">${safeLabel}</option>`;
            }))
            .join('');

        function makeRow(index) {
            const tr = document.createElement('tr');
            tr.className = 'order-item-row';
            tr.innerHTML = `
                <td>
                    <select name="items[${index}][product_id]" class="form-control product-select" required>
                        ${productOptionsHtml}
                    </select>
                </td>
                <td><input type="text" class="form-control item-stock" value="0" readonly></td>
                <td><input type="number" min="1" name="items[${index}][quantity]" class="form-control item-qty" value="1" required></td>
                <td><input type="number" class="form-control item-price" step="0.01" value="0" readonly></td>
                <td><input type="number" class="form-control item-subtotal" step="0.01" value="0" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item-btn order-btn-stable">Remove</button></td>
            `;
            return tr;
        }

        function renumberRows() {
            const rows = itemsBody.querySelectorAll('.order-item-row');
            rows.forEach((row, index) => {
                const productSelect = row.querySelector('.product-select');
                const qtyInput = row.querySelector('.item-qty');
                productSelect.name = `items[${index}][product_id]`;
                qtyInput.name = `items[${index}][quantity]`;
            });
        }

        function updateRow(row) {
            const productSelect = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.item-qty');
            const stockInput = row.querySelector('.item-stock');
            const priceInput = row.querySelector('.item-price');
            const subtotalInput = row.querySelector('.item-subtotal');

            const selected = productSelect.options[productSelect.selectedIndex];
            const price = selected ? parseFloat(selected.dataset.price || '0') : 0;
            const stock = selected ? parseInt(selected.dataset.stock || '0', 10) : 0;
            let qty = parseInt(qtyInput.value || '0', 10);
            if (!Number.isFinite(qty) || qty < 1) qty = 1;
            if (stock > 0 && qty > stock) qty = stock;
            qtyInput.value = qty;

            const subtotal = price * qty;
            stockInput.value = stock;
            priceInput.value = price.toFixed(2);
            subtotalInput.value = subtotal.toFixed(2);
        }

        function updateOrderTotal() {
            const subtotals = itemsBody.querySelectorAll('.item-subtotal');
            let total = 0;
            subtotals.forEach((s) => total += parseFloat(s.value || '0'));
            orderAmount.value = total.toFixed(2);
        }

        function updateAll() {
            const rows = itemsBody.querySelectorAll('.order-item-row');
            rows.forEach(updateRow);
            updateOrderTotal();
        }

        itemsBody.addEventListener('change', (e) => {
            if (e.target.classList.contains('product-select') || e.target.classList.contains('item-qty')) {
                const row = e.target.closest('.order-item-row');
                if (row) {
                    updateRow(row);
                    updateOrderTotal();
                }
            }
        });

        itemsBody.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item-btn')) {
                const rows = itemsBody.querySelectorAll('.order-item-row');
                if (rows.length <= 1) return;
                e.target.closest('.order-item-row')?.remove();
                renumberRows();
                updateAll();
            }
        });

        addItemBtn?.addEventListener('click', () => {
            const index = itemsBody.querySelectorAll('.order-item-row').length;
            itemsBody.appendChild(makeRow(index));
            updateAll();
        });

        updateAll();
    })();
</script>
</body>
</html>

