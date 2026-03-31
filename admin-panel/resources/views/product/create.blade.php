<!DOCTYPE html>
@extends('layouts.app')
@section('title', 'Add Product')

@section('navbar_right')
  <button class="btn btn-outline-secondary btn-sm" onclick="history.back()"><i class="bi bi-arrow-left"></i> Back</button>
  <button class="btn btn-outline-primary btn-sm" id="draftBtn"><i class="bi bi-save"></i> Save Draft</button>
@endsection

@push('styles')
<style>
.product-create-container { max-width: 1200px; }
.small-muted { font-size: 12px; color: var(--muted); }

/* Tabs */
.product-tabs { margin-bottom: 16px; }
.product-tabs .nav-tabs { border-bottom: none; gap: 8px; }
.product-tabs .nav-tabs .nav-link {
  border: 1px solid var(--border);
  color: var(--muted);
  padding: 10px 14px;
  border-radius: 10px;
  transition: all .18s ease;
  background: var(--card-bg);
  box-shadow: var(--shadow);
}
.product-tabs .nav-tabs .nav-link:hover { border-color: rgba(37,99,235,.35); color: var(--primary); }
.product-tabs .nav-tabs .nav-link.active {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}

/* Section cards */
.section-card{
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px;
  margin-bottom: 16px;
  box-shadow: var(--shadow);
}
.section-head{ display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.section-head .icon{
  width:40px; height:40px; border-radius:10px;
  display:flex; align-items:center; justify-content:center;
  background: var(--primary);
  color:#fff; font-size:16px; flex:0 0 40px;
}
.section-title{ font-weight:800; color:var(--text); }
.section-sub{ font-size:12.5px; color:var(--muted); margin-left:auto; }

/* Price breakdown */
.price-breakdown { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:10px; }
.price-chip {
  background: var(--bg);
  border: 1px solid var(--border);
  padding: 10px 12px;
  border-radius: 12px;
  font-weight: 800;
  color: var(--text);
  min-width: 140px;
  text-align:center;
}

/* Uploader + thumbs */
.uploader {
  border-radius: 12px;
  border: 1.5px dashed var(--border);
  padding: 20px;
  background: var(--bg);
  text-align: center;
  cursor: pointer;
  transition: all .18s;
}
.uploader.dragover {
  background: var(--primary-light);
  border-color: rgba(37,99,235,.45);
  box-shadow: var(--shadow-md);
}
.thumb-row{ display:flex; flex-wrap:wrap; gap:10px; margin-top:12px; }
.thumb{
  width:86px; height:86px;
  border-radius:12px;
  overflow:hidden;
  position:relative;
  background:#fff;
  border:1px solid var(--border);
  box-shadow: var(--shadow);
}
.thumb img{ width:100%; height:100%; object-fit:cover; display:block; }
.thumb .rm{
  position:absolute; top:6px; right:6px;
  background: rgba(15,23,42,.75);
  color:#fff;
  border-radius:8px;
  padding:6px;
  cursor:pointer;
}

/* Tags */
.tags-input {
  display:flex; flex-wrap:wrap; gap:8px; align-items:center;
  min-height:44px; padding:6px;
  border-radius:8px; border:1px solid var(--border);
  background:#fff;
}
.tag {
  background: var(--primary-light);
  padding: 6px 10px;
  border-radius: 999px;
  font-weight: 700;
  color: var(--primary);
  display:flex; gap:8px; align-items:center;
  font-size: 12.5px;
}
.tag .remove { cursor:pointer; color: rgba(37,99,235,.8); display:inline-flex; padding:2px; }

/* Preview table */
.preview-wrapper { max-height:260px; overflow:auto; border-radius:10px; border:1px solid var(--border); background:#fff; box-shadow: var(--shadow); }
.preview-table { width:100%; border-collapse:collapse; }
.preview-table th, .preview-table td { padding:8px 10px; border-bottom:1px solid var(--border); font-size:13px; text-align:left; }
.row-error { background: rgba(220,38,38,0.06); }
.badge-error { background:#fee2e2; color:#991b1b; border-radius:999px; padding:4px 8px; font-weight:800; }

/* Action bar */
.sticky-actions { display:flex; justify-content:flex-end; gap:12px; align-items:center; margin-top:16px; margin-bottom:18px; }
</style>
@endpush

@section('content')
<div class="container-fluid product-create-container">

{{--
/* Form controls */
.form-label { font-weight: 600; color: #334155; font-size: 13px; }
.form-control, .form-select {
    border-radius: 10px; border: 1px solid #e6edf6; padding: 10px 12px;
    background: #fff; color:var(--text-dark);
}
.form-control:focus, .form-select:focus {
    border-color: var(--sidebar-grad-solid);
    box-shadow: 0 0 0 6px rgba(30,58,138,0.06);
}

/* Price breakdown */
.price-breakdown {
    display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-top:10px;
}
.price-chip {
    background: linear-gradient(90deg,#fbfdff,#ffffff);
    border:1px solid #e6edf6;padding:10px 12px;border-radius:12px;font-weight:700;
    color:var(--text-dark);
    min-width:140px;text-align:center;
}

/* Uploader enhancements */
.uploader {
    border-radius:12px;border:1px dashed #e6edf6;padding:20px;background:linear-gradient(180deg,#fbfdff,#ffffff);
    text-align:center;cursor:pointer;transition:all .18s;
}
.uploader.dragover { background: linear-gradient(90deg,#eef2ff,#ffffff);box-shadow:0 12px 30px rgba(30,58,138,0.06);transform:translateY(-3px); border-color: rgba(30,58,138,0.25); }

/* Thumbs improved */
.thumb-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px}
.thumb{width:86px;height:86px;border-radius:12px;overflow:hidden;position:relative;background:#fff;border:1px solid #eef2ff;box-shadow:0 8px 22px rgba(15,23,42,0.04)}
.thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .28s}
.thumb:hover img{transform:scale(1.06)}
.thumb .rm{position:absolute;top:6px;right:6px;background:var(--sidebar-grad-solid);color:#fff;border-radius:8px;padding:6px;cursor:pointer}

/* Tags / chips */
.tags-input { display:flex;flex-wrap:wrap;gap:8px;align-items:center;min-height:44px;padding:6px;border-radius:10px;border:1px solid #e6edf6;background:#fff; }
.tag { background: linear-gradient(90deg,#eef2ff,#f8fafc); padding:6px 10px;border-radius:999px;font-weight:600;color:#334155;display:flex;gap:8px;align-items:center;font-size:13px;box-shadow: 0 6px 16px rgba(15,23,42,0.04); }
.tag .remove { cursor:pointer;color:#94a3b8;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;padding:2px }

/* Bulk area */
.bulk-card .uploader { padding:28px; border-radius:14px; background: linear-gradient(180deg,#ffffff,#fbfdff); border:1px solid rgba(30,58,138,0.06); }
.bulk-actions { display:flex;align-items:center;gap:10px;flex-wrap:wrap; }

/* Preview table */
.preview-wrapper { max-height:260px; overflow:auto; border-radius:10px; border:1px solid #e6edf6; background:#fff; box-shadow:0 6px 20px rgba(15,23,42,0.03); }
.preview-table { width:100%; border-collapse:collapse; }
.preview-table th, .preview-table td { padding:8px 10px; border-bottom:1px solid #f1f5f9; font-size:13px; text-align:left; }
.row-error { background: rgba(255,100,100,0.06); }
.badge-error { background:#fee2e2;color:#991b1b;border-radius:999px;padding:4px 8px;font-weight:700; }

/* Action bar */
.sticky-actions {
    display:flex; justify-content:flex-end; gap:12px; align-items:center;
    background: transparent; margin-top:16px; margin-bottom:30px;
}

/* Bulk results */
.bulk-results {
    margin-top:12px;
}
.results-table th { background: var(--sidebar-grad); color:#fff; }

/* Responsive tweaks */
@media(max-width:992px){
    .sidebar { display:none; }
    .content { margin-left:0; padding:20px; }
    .topbar,.form-card { width:100%; padding:20px; }
    .nav-tabs .nav-link { padding:10px 12px; margin-right:6px; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="brand">
   <img src="{{ asset('uploads/admin/'.admin_setting()->profile_image) }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">
   <span>{{ admin_setting()->company_name ?? 'Admin Panel' }}</span>
  </div>

  <div class="menu">
   
    <a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Products</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('product.index') }}"><i class="bi bi-list-ul"></i> Product List</a>
        <a href="{{ route('product.create') }}" class="active"><i class="bi bi-plus-circle"></i> Add Product</a>
        <a href="{{ route('product.status') }}"><i class="bi bi-toggle-on"></i> Product Status</a>
      </div>
    </div>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-award"></i><span>Brands</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('brands.index') }}"><i class="bi bi-list-ul"></i> Brands List</a>
        <a href="{{ route('brands.create') }}"><i class="bi bi-plus-circle"></i> Add Brand</a>
        <a href="{{ route('brands.status') }}"><i class="bi bi-toggle-on"></i> Brand Status</a>
      </div>
    </div>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-tag"></i><span>Categories</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('categories.index') }}"><i class="bi bi-list-ul"></i> Categories List</a>
        <a href="{{ route('categories.create') }}"><i class="bi bi-plus-circle"></i> Add Category</a>
        <a href="{{ route('categories.status') }}"><i class="bi bi-toggle-on"></i> Category Status</a>
        <a href="{{ route('subcategories.index') }}"><i class="bi bi-list-ul"></i> SubCategories</a>
        <a href="{{ route('subcategories.create') }}"><i class="bi bi-plus-circle"></i> Add SubCategory</a>
      </div>
    </div>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-building"></i><span>Warehouses</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('warehouse.index') }}"><i class="bi bi-list-ul"></i> Warehouse List</a>
        <a href="{{ route('warehouse.create') }}"><i class="bi bi-plus-circle"></i> Add Warehouse</a>
        <a href="{{ route('warehouse.status') }}"><i class="bi bi-toggle-on"></i> Warehouse Status</a>
      </div>
    </div>

    <a href="{{ route('inventory.index') }}"><i class="bi bi-layers"></i><span>Inventory</span></a>
    <a href="{{ route('customers.index') }}"><i class="bi bi-people"></i><span>Customers</span></a>
    <a href="{{ route('store.store_index') }}"><i class="bi bi-shop"></i><span>Parties / Stores</span></a>
    <a href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i><span>Orders &amp; Invoices</span></a>
    <a href="{{ route('cities.index') }}"><i class="bi bi-geo-alt"></i><span>Cities &amp; Localities</span></a>
    <a href="{{ route('sales.person.index') }}"><i class="bi bi-person-badge"></i><span>Sales Persons</span></a>
    <a href="{{ route('delivery.person.index') }}"><i class="bi bi-truck"></i><span>Delivery Persons</span></a>
    <a href="{{ route('attendance.index') }}"><i class="bi bi-calendar-check"></i><span>Attendance</span></a>
    <a href="{{ route('salary.salary_index') }}"><i class="bi bi-cash-stack"></i><span>Salary &amp; Incentives</span></a>
    <a href="{{ route('report.report_index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
    <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
    <a href="{{ route('logout') }}"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
  </div>
</div>
<!-- MAIN CONTENT (ENHANCED RIGHT SIDE) -->
<div class="content">
    <div class="inner-content">

        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Add Product</h5>
                <div class="small-muted">Enterprise FMCG Management</div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="history.back()"><i class="bi bi-arrow-left"></i> Back</button>
                <button class="btn btn-outline-primary btn-sm" id="draftBtn"><i class="bi bi-save"></i> Save Draft</button>
            </div>
        </div>
--}}

        <!-- Tabs -->
        <div class="product-tabs">
            <ul class="nav nav-tabs" id="productModeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab" aria-controls="single" aria-selected="true">
                        <i class="bi bi-person-fill me-2"></i> Single Product Entry
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab" aria-controls="bulk" aria-selected="false">
                        <i class="bi bi-upload me-2"></i> Bulk Product Upload
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ route('product.export') }}">
                        <i class="bi bi-download me-2"></i> Bulk Export
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content">

            <!-- TAB 1: SINGLE PRODUCT ENTRY -->
            <div class="tab-pane fade show active" id="single" role="tabpanel" aria-labelledby="single-tab">

<form id="productForm"
      action="{{ route('product.store') }}"
      method="POST"
      enctype="multipart/form-data">
@csrf

<div id="formAlerts" class="mb-3"></div>

<!-- GENERAL DETAILS -->
<div class="section-card">
  <div class="section-head">
    <div class="icon"><i class="bi bi-info-circle"></i></div>
    <div>
      <div class="section-title">General Details</div>
      <div class="small-muted">Basic product information</div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Product Name</label>
      <input type="text" name="name" class="form-control" required placeholder="Enter product name">
    </div>
    <div class="col-md-3">
      <label class="form-label">SKU</label>
      <input type="text" name="sku" class="form-control" required placeholder="Enter SKU">
    </div>
    <div class="col-md-3">
      <label class="form-label">Item Code</label>
      <input type="text" name="item_code" class="form-control" placeholder="Enter Item Code">
    </div>

    <div class="col-md-4">
      <label class="form-label">Category</label>
      <div class="input-group">
        <select id="categorySelect" name="category" class="form-select" required>
          <option value="">Select Category</option>
          @foreach($categories as $category)
            <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
          @endforeach
        </select>
        <button class="btn btn-outline-primary" type="button" onclick="new bootstrap.Modal('#categoryModal').show()">+New</button>
      </div>
    </div>

    <div class="col-md-4">
      <label class="form-label">Brand</label>
      <div class="input-group">
        <select id="brandSelect" name="brand" class="form-select" required>
          <option value="">Select Brand</option>
          @foreach($brands as $brand)
            <option value="{{ $brand->name }}">{{ $brand->name }}</option>
          @endforeach
        </select>
        <button class="btn btn-outline-primary" type="button" onclick="new bootstrap.Modal('#brandModal').show()">+New</button>
      </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Unit</label>
        <select name="unit" class="form-select" required>
            <option value="PCS">PCS</option>
            <option value="BOX">BOX</option>
            <option value="BUNCH">BUNCH</option>
            <option value="KG">KG</option>
            <option value="LTR">LTR</option>
            <option value="PACKET">PACKET</option>
        </select>
    </div>

    <div class="col-md-12">
      <label class="form-label">Item Description</label>
      <textarea name="item_description" class="form-control" rows="2" placeholder="Enter product description"></textarea>
    </div>
  </div>
</div>

<!-- MULTIPLE IMAGES -->
<div class="section-card">
    <div class="section-head">
        <div class="icon"><i class="bi bi-images"></i></div>
        <div>
            <div class="section-title">Product Images</div>
            <div class="small-muted">Upload multiple product images</div>
        </div>
    </div>
    <div class="uploader" id="imageUploader" onclick="document.getElementById('product_images').click()">
        <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
        <p class="mb-0 mt-2">Click to upload or drag and drop</p>
        <small class="text-muted">JPG, PNG, WEBP (Max 3MB each)</small>
        <input type="file" id="product_images" name="images[]" multiple class="d-none" accept="image/*" onchange="previewImages(this)">
    </div>
    <div class="thumb-row" id="imagePreview"></div>
</div>

<!-- PRICE DETAILS -->
<div class="section-card">
  <div class="section-head">
    <div class="icon"><i class="bi bi-currency-dollar"></i></div>
    <div>
      <div class="section-title">Price Details</div>
      <div class="small-muted">Pricing and Tax information</div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">MRP (₹)</label>
      <input type="number" name="mrp" id="mrp" class="form-control" step="0.01" required placeholder="0">
    </div>

    {{-- Purchase Price with Excl./Incl. Tax toggle --}}
    <div class="col-md-3">
      <label class="form-label">Purchase Price (₹)</label>
      <div class="input-group">
        <span class="input-group-text">₹</span>
        <input type="number" name="purchase_price" id="purchasePrice" class="form-control" step="0.01" required placeholder="0">
        <select id="purchaseTaxType" name="purchase_tax_type" class="form-select" style="max-width:130px;border-left:0;">
          <option value="excl">Excl. Tax</option>
          <option value="incl">Incl. Tax</option>
        </select>
      </div>
      <small class="text-muted" id="purchaseBaseHint" style="display:none;">Base (excl. tax): ₹<span id="purchaseBaseVal">0.00</span></small>
    </div>

    {{-- Sell Price with Excl./Incl. Tax toggle --}}
    <div class="col-md-3">
      <label class="form-label">Sell Price (₹)</label>
      <div class="input-group">
        <span class="input-group-text">₹</span>
        <input type="number" name="sell_price" id="sellPrice" class="form-control" step="0.01" required placeholder="0">
        <select id="sellTaxType" name="sell_tax_type" class="form-select" style="max-width:130px;border-left:0;">
          <option value="excl">Excl. Tax</option>
          <option value="incl">Incl. Tax</option>
        </select>
      </div>
      <small class="text-muted" id="sellBaseHint" style="display:none;">Base (excl. tax): ₹<span id="sellBaseVal">0.00</span></small>
    </div>

    <div class="col-md-3">
      <label class="form-label">HSN Code</label>
      <input type="text" name="hsn_code" class="form-control">
    </div>

    <div class="col-md-3">
      <label class="form-label">GST Rate</label>
      <select name="gst_percent" id="gstSelect" class="form-select">
        <option value="0">GST @ 0%</option>
        <option value="0.25">GST @ 0.25%</option>
        <option value="1.5">GST @ 1.5%</option>
        <option value="3">GST @ 3%</option>
        <option value="5">GST @ 5%</option>
        <option value="6">GST @ 6%</option>
        <option value="12">GST @ 12%</option>
        <option value="13">GST @ 13%</option>
        <option value="18">GST @ 18%</option>
        <option value="28">GST @ 28%</option>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Cess</label>
      <select name="cess_percent" id="cessSelect" class="form-select">
        <option value="0">No Cess</option>
        <option value="12">Cess @ 12%</option>
        <option value="60">Cess @ 60%</option>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Offer Text</label>
      <input type="text" name="offer_text" class="form-control" placeholder="e.g. Buy 1 Get 1">
    </div>

    {{-- Live Price Summary --}}
    <div class="col-md-12">
      <div class="price-breakdown">
        <div class="price-chip">GST Amt: ₹<span id="gstAmount">0.00</span></div>
        <div class="price-chip">Cess Amt: ₹<span id="cessAmount">0.00</span></div>
        <div class="price-chip" id="discountChip" style="display:none;">Discount: ₹<span id="discountAmtChip">0.00</span></div>
        <div class="price-chip">Final Price: ₹<span id="finalPrice">0.00</span></div>
        <div class="price-chip">Margin: ₹<span id="marginAmount">0.00</span></div>
        <div class="price-chip" id="marginChip">Margin %: <span id="marginPercent">0.00</span>%</div>
      </div>
      <div id="negativeMarginWarn" class="text-danger small mt-1" style="display:none;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>Warning: Negative margin — sell price is below purchase price.
      </div>
    </div>
  </div>
</div>

<!-- STOCK DETAILS -->
<div class="section-card">
  <div class="section-head">
    <div class="icon"><i class="bi bi-database"></i></div>
    <div>
      <div class="section-title">Stock Details</div>
      <div class="small-muted">Warehouse inventory management</div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Warehouse</label>
      <select name="warehouse_id" id="warehouseSelect" class="form-select" required>
        <option value="">Select Warehouse</option>
        @foreach($warehouses as $wh)
          <option value="{{ $wh->id }}">{{ $wh->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Availability Units</label>
      <input type="number" name="available_units" id="available_units" class="form-control" value="0" min="0">
      <small class="text-info" id="currentStockHint" style="display:none;">Current stock in selected warehouse: <span id="currentStockVal">0</span></small>
    </div>
    <div class="col-md-6">
        <label class="form-label">Product Status</label>
        <select name="status" class="form-select">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>
  </div>
</div>

<!-- UNIT CONVERSION -->
<div class="section-card">
    <div class="section-head">
        <div class="icon"><i class="bi bi-arrow-left-right"></i></div>
        <div>
            <div class="section-title">Unit Conversion</div>
            <div class="small-muted">Define how many base units make a secondary unit</div>
        </div>
        <div class="section-sub d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addUnitRow()">+ Add Conversion</button>
            <button type="button" id="addDiscountBtn" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-tag me-1"></i>+ Add Discount
            </button>
            <button type="button" id="removeDiscountBtn" class="btn btn-sm btn-outline-danger" style="display:none;">
                <i class="bi bi-x me-1"></i>Remove Discount
            </button>
        </div>
    </div>

    {{-- Discount Fields (collapsible) --}}
    <div id="discountFields" style="display:none;" class="mb-3">
        <div class="row g-3 align-items-end p-3 rounded" style="background:#fffbeb;border:1px solid #fde68a;">
            <div class="col-md-3">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" id="discountType" class="form-select">
                    <option value="percent">Percentage (%)</option>
                    <option value="flat">Flat (₹)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount Value</label>
                <input type="number" name="discount_value" id="discountValue" class="form-control" step="0.01" min="0" placeholder="Enter discount">
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount Amount</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">₹</span>
                    <input type="text" id="discountAmountDisplay" class="form-control bg-light" readonly placeholder="0.00" style="font-weight:700;">
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div id="discountError" class="text-danger small" style="display:none;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Discount exceeds sell price!
                </div>
            </div>
        </div>
    </div>
    {{-- Hidden discount fields for form submission --}}
    <input type="hidden" name="discount" id="discountHidden" value="0">
    <input type="hidden" name="discount_amount" id="discountAmountHidden" value="0">

    <div id="unitConversionRows">
        <!-- Example: 1 BOX = 50 PCS -->
        <div class="row g-2 mb-2 unit-row align-items-end">
            <div class="col">
                <label class="form-label d-none">Unit Name</label>
                <input type="text" name="unit_names[]" class="form-control" placeholder="Unit (e.g. BOX)">
            </div>
            <div class="col-auto pt-4 text-muted">=</div>
            <div class="col">
                <label class="form-label d-none">Conversion Value</label>
                <input type="number" name="conversion_values[]" class="form-control conversion-qty" placeholder="Qty" oninput="calculateUnitTotals()">
            </div>
            <div class="col">
                <label class="form-label d-none">Base Unit</label>
                <select name="base_units[]" class="form-select">
                    <option value="PCS">PCS</option>
                    <option value="BUNCH">BUNCH</option>
                    <option value="KG">KG</option>
                    <option value="LTR">LTR</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="font-size: 13px;">₹</span>
                    <input type="text" class="form-control bg-light border-start-0 row-total-price" readonly placeholder="0.00" style="font-weight:700;">
                </div>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.unit-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    </div>
</div>

<div class="sticky-actions">
  <button type="submit" class="btn btn-primary btn-lg save-btn shadow">
    <i class="bi bi-check-circle me-2"></i>Save Product
  </button>
</div>

</form>

            </div>

            <!-- TAB 2: BULK UPLOAD -->
            <div class="tab-pane fade" id="bulk" role="tabpanel" aria-labelledby="bulk-tab">

                {{-- IMPORT SECTION --}}
                <div class="section-card">
                    <div class="section-head">
                        <div class="icon"><i class="bi bi-cloud-upload"></i></div>
                        <div>
                            <div class="section-title">Import Products</div>
                            <div class="small-muted">Upload an Excel (.xlsx) file to create or update products</div>
                        </div>
                    </div>

                    <form id="bulkForm" action="{{ route('product.bulk') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Excel File (.xlsx)</label>
                                <input type="file" name="bulk_file" accept=".xlsx,.xls" class="form-control" required id="bulkFileInput">
                                <small class="text-muted mt-1 d-block">Required columns: <strong>Product Name</strong>, <strong>SKU</strong> — all others optional</small>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary px-4" id="importBtn">
                                    <i class="bi bi-cloud-upload me-2"></i>Import Products
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Result summary (shown after import) --}}
                    <div id="importResult" class="mt-4" style="display:none;">
                        <hr class="my-3">
                        <div class="small fw-bold text-muted mb-3">Import Summary</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="price-chip" style="border-color:#bbf7d0;">
                                    <div class="small text-muted mb-1">Created</div>
                                    <div class="fs-3 fw-bold text-success" id="resImported">0</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="price-chip" style="border-color:#bfdbfe;">
                                    <div class="small text-muted mb-1">Updated</div>
                                    <div class="fs-3 fw-bold text-primary" id="resUpdated">0</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="price-chip" style="border-color:#fecaca;">
                                    <div class="small text-muted mb-1">Failed</div>
                                    <div class="fs-3 fw-bold text-danger" id="resFailed">0</div>
                                </div>
                            </div>
                        </div>
                        <div id="importErrors" class="mt-3" style="display:none;">
                            <div class="small text-danger fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>Row Errors:</div>
                            <ul id="importErrorList" class="small text-danger ps-3 mb-0"></ul>
                        </div>
                    </div>

                    {{-- Column reference --}}
                    <div class="mt-4 p-3 rounded" style="background:#f8fafc;border:1px solid #e6edf6;">
                        <div class="small fw-bold text-muted mb-2"><i class="bi bi-info-circle me-1"></i>Expected column headers (row 1):</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Product Name *','SKU *','Item Code','Category','Brand','Unit','MRP','Purchase Price','Sell Price','GST %','Cess %','Discount Type','Discount Value','Warehouse','Stock','Status'] as $col)
                                <span class="badge" style="background:#eef2ff;color:#3730a3;font-weight:600;font-size:11px;padding:5px 10px;">{{ $col }}</span>
                            @endforeach
                        </div>
                        <div class="small text-muted mt-2">
                            <span class="text-danger">*</span> Required &nbsp;·&nbsp;
                            Discount Type: <code>percent</code> or <code>flat</code> &nbsp;·&nbsp;
                            Status: <code>Active</code> or <code>Inactive</code> &nbsp;·&nbsp;
                            Duplicate SKU → row is <strong>updated</strong>
                        </div>
                    </div>
                </div>

            </div>

        </div><!-- /.tab-content -->
<!-- SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.15);">
      <div style="padding:30px;text-align:center;">
        <div style="width:80px;height:80px;margin:0 auto 20px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;">
          <i class="bi bi-check-circle-fill text-success" style="font-size:45px;"></i>
        </div>
        <h5 style="color:#0f172a;margin-bottom:10px;font-weight:700;font-size:18px;">Success</h5>
        <p id="successMsg" style="color:#6b7280;margin-bottom:20px;font-size:14px;">Data saved successfully</p>
        <button type="button" class="btn" style="background:linear-gradient(135deg,#10b981,#059669);border:none;color:white;font-weight:600;padding:10px 30px;width:100%;" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- ERROR MODAL -->
<div class="modal fade" id="errorModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.15);">
      <div style="padding:30px;text-align:center;">
        <div style="width:60px;height:60px;margin:0 auto 20px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;">
          <i class="bi bi-x-circle-fill text-danger fs-5"></i>
        </div>
        <h5 style="color:#0f172a;margin-bottom:10px;font-weight:700;">Error</h5>
        <p id="errorMsg" style="color:#6b7280;margin-bottom:20px;font-size:14px;">Something went wrong</p>
        <button type="button" class="btn" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;color:white;font-weight:600;" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>


@push('scripts')
<script>
const successModalEl = document.getElementById('successModal');
const errorModalEl   = document.getElementById('errorModal');
const successModal = new bootstrap.Modal(successModalEl);
const errorModal   = new bootstrap.Modal(errorModalEl);

// ==================
// Image Preview
// ==================
let selectedFiles = [];

function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    const files = Array.from(input.files);
    
    files.forEach(file => {
        selectedFiles.push(file);
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'thumb';
            div.innerHTML = `
                <img src="${e.target.result}">
                <div class="rm" onclick="removeImage(this, ${selectedFiles.length - 1})"><i class="bi bi-x"></i></div>
            `;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

function removeImage(btn, index) {
    btn.parentElement.remove();
}

// ==================
// Price Calculations (GST + Cess + Excl/Incl Tax + Discount)
// ==================
const purchase         = document.getElementById('purchasePrice');
const sell             = document.getElementById('sellPrice');
const gstSelect        = document.getElementById('gstSelect');
const cessSelect       = document.getElementById('cessSelect');
const purchaseTaxType  = document.getElementById('purchaseTaxType');
const sellTaxType      = document.getElementById('sellTaxType');
const discountType     = document.getElementById('discountType');
const discountValue    = document.getElementById('discountValue');
const discountHidden   = document.getElementById('discountHidden');
const discountAmtHidden= document.getElementById('discountAmountHidden');
const discountAmtDisp  = document.getElementById('discountAmountDisplay');
const discountError    = document.getElementById('discountError');
const discountChip     = document.getElementById('discountChip');
const discountAmtChip  = document.getElementById('discountAmtChip');
const gstAmountEl      = document.getElementById('gstAmount');
const cessAmountEl     = document.getElementById('cessAmount');
const finalPriceEl     = document.getElementById('finalPrice');
const marginEl         = document.getElementById('marginAmount');
const marginPctEl      = document.getElementById('marginPercent');
const marginChip       = document.getElementById('marginChip');
const negWarn          = document.getElementById('negativeMarginWarn');
const purchaseBaseHint = document.getElementById('purchaseBaseHint');
const purchaseBaseVal  = document.getElementById('purchaseBaseVal');
const sellBaseHint     = document.getElementById('sellBaseHint');
const sellBaseVal      = document.getElementById('sellBaseVal');

// Shared: break down an entered price into base + gst + cess
function breakdownPrice(entered, taxType, gstPct, cessPct) {
    const totalTax = gstPct + cessPct;
    let base, gstAmt, cessAmt;
    if (taxType === 'incl' && totalTax > 0) {
        base    = entered / (1 + totalTax / 100);
        gstAmt  = base * gstPct  / 100;
        cessAmt = base * cessPct / 100;
    } else {
        base    = entered;
        gstAmt  = base * gstPct  / 100;
        cessAmt = base * cessPct / 100;
    }
    return { base, gstAmt, cessAmt, total: base + gstAmt + cessAmt };
}

function calculatePrices() {
    const pEntered  = parseFloat(purchase.value) || 0;
    const sEntered  = parseFloat(sell.value)     || 0;
    const gstPct    = parseFloat(gstSelect.value)   || 0;
    const cessPct   = parseFloat(cessSelect.value)  || 0;
    const pType     = purchaseTaxType.value;
    const sType     = sellTaxType.value;
    const discActive = document.getElementById('discountFields').style.display !== 'none';
    const dType     = discountType ? discountType.value : 'flat';
    const dVal      = parseFloat(discountValue ? discountValue.value : 0) || 0;

    const pBreak = breakdownPrice(pEntered, pType, gstPct, cessPct);
    const sBreak = breakdownPrice(sEntered, sType, gstPct, cessPct);

    // Base hints for incl. tax mode
    if (pType === 'incl' && gstPct + cessPct > 0) {
        purchaseBaseHint.style.display = 'block';
        purchaseBaseVal.innerText = pBreak.base.toFixed(2);
    } else { purchaseBaseHint.style.display = 'none'; }

    if (sType === 'incl' && gstPct + cessPct > 0) {
        sellBaseHint.style.display = 'block';
        sellBaseVal.innerText = sBreak.base.toFixed(2);
    } else { sellBaseHint.style.display = 'none'; }

    // Discount calculation
    let discAmt = 0;
    if (discActive && dVal > 0) {
        discAmt = dType === 'percent' ? (sBreak.total * dVal / 100) : dVal;
    }

    // Validate: discount must not exceed sell total
    const discValid = discAmt <= sBreak.total;
    if (discountError) discountError.style.display = (!discValid && discActive && dVal > 0) ? 'block' : 'none';
    if (!discValid) discAmt = 0;

    // Update discount display
    if (discountAmtDisp) discountAmtDisp.value = discAmt.toFixed(2);
    if (discountHidden)  discountHidden.value  = discAmt.toFixed(2);
    if (discountAmtHidden) discountAmtHidden.value = discAmt.toFixed(2);

    // Discount chip
    if (discountChip) {
        discountChip.style.display = (discActive && discAmt > 0) ? 'flex' : 'none';
        if (discountAmtChip) discountAmtChip.innerText = discAmt.toFixed(2);
    }

    // Final price = sell total - discount
    const finalSell = sBreak.total - discAmt;

    // Margin on base prices (excl. tax), after discount
    const finalSellBase = sBreak.base - discAmt;
    const margin    = finalSellBase - pBreak.base;
    const marginPct = pBreak.base > 0 ? (margin / pBreak.base) * 100 : 0;

    gstAmountEl.innerText  = sBreak.gstAmt.toFixed(2);
    cessAmountEl.innerText = sBreak.cessAmt.toFixed(2);
    finalPriceEl.innerText = finalSell.toFixed(2);
    marginEl.innerText     = margin.toFixed(2);
    marginPctEl.innerText  = marginPct.toFixed(2);

    if (margin < 0) {
        negWarn.style.display  = 'block';
        marginChip.style.color = '#dc2626';
    } else {
        negWarn.style.display  = 'none';
        marginChip.style.color = '';
    }

    calculateUnitTotals();
}

// Discount button toggle
document.getElementById('addDiscountBtn').addEventListener('click', function() {
    document.getElementById('discountFields').style.display = 'block';
    this.style.display = 'none';
    document.getElementById('removeDiscountBtn').style.display = 'inline-flex';
    calculatePrices();
});
document.getElementById('removeDiscountBtn').addEventListener('click', function() {
    document.getElementById('discountFields').style.display = 'none';
    this.style.display = 'none';
    document.getElementById('addDiscountBtn').style.display = 'inline-flex';
    if (discountValue) discountValue.value = '';
    if (discountHidden) discountHidden.value = '0';
    if (discountAmtHidden) discountAmtHidden.value = '0';
    calculatePrices();
});

[purchase, sell, discountValue].forEach(el => {
    if (el) el.addEventListener('input', calculatePrices);
});
[gstSelect, cessSelect, purchaseTaxType, sellTaxType, discountType].forEach(el => {
    if (el) el.addEventListener('change', calculatePrices);
});

// ==================
// Warehouse Stock Check
// ==================
const whSelect = document.getElementById('warehouseSelect');
if(whSelect) {
    whSelect.addEventListener('change', function() {
        const whId = this.value;
        const pid = ""; // New product
        if(whId) {
            fetch(`{{ route('product.warehouse.stock') }}?warehouse_id=${whId}&product_id=${pid}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                const hint = document.getElementById('currentStockHint');
                const val = document.getElementById('currentStockVal');
                hint.style.display = 'block';
                val.innerText = data.stock;
            });
        }
    });
}

// ==================
// Unit Conversions
// ==================
function addUnitRow() {
    const container = document.getElementById('unitConversionRows');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 unit-row align-items-end';
    row.innerHTML = `
        <div class="col">
            <input type="text" name="unit_names[]" class="form-control" placeholder="Unit (e.g. BOX)">
        </div>
        <div class="col-auto pt-2 text-muted">=</div>
        <div class="col">
            <input type="number" name="conversion_values[]" class="form-control conversion-qty" placeholder="Qty" oninput="calculateUnitTotals()">
        </div>
        <div class="col">
            <select name="base_units[]" class="form-select">
                <option value="PCS">PCS</option>
                <option value="BUNCH">BUNCH</option>
                <option value="KG">KG</option>
                <option value="LTR">LTR</option>
            </select>
        </div>
        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0" style="font-size: 13px;">₹</span>
                <input type="text" class="form-control bg-light border-start-0 row-total-price" readonly placeholder="0.00" style="font-weight:700;">
            </div>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.unit-row').remove()"><i class="bi bi-trash"></i></button>
        </div>
    `;
    container.appendChild(row);
}

function calculateUnitTotals() {
    const sEntered = parseFloat(document.getElementById('sellPrice').value) || 0;
    const gstPct   = parseFloat(document.getElementById('gstSelect').value)  || 0;
    const cessPct  = parseFloat(document.getElementById('cessSelect').value) || 0;
    const sType    = document.getElementById('sellTaxType').value;
    const sBreak   = breakdownPrice(sEntered, sType, gstPct, cessPct);

    const discFieldsEl = document.getElementById('discountFields');
    const discActive   = discFieldsEl && discFieldsEl.style.display !== 'none';
    const dTypeEl  = document.getElementById('discountType');
    const dValEl   = document.getElementById('discountValue');
    const dType    = dTypeEl ? dTypeEl.value : 'flat';
    const dVal     = parseFloat(dValEl ? dValEl.value : 0) || 0;

    let discAmt = 0;
    if (discActive && dVal > 0) {
        discAmt = dType === 'percent' ? (sBreak.total * dVal / 100) : dVal;
        if (discAmt > sBreak.total) discAmt = 0;
    }

    const finalSell = sBreak.total - discAmt;

    document.querySelectorAll('.unit-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.conversion-qty').value) || 0;
        row.querySelector('.row-total-price').value = qty > 0 ? (finalSell * qty).toFixed(2) : '0.00';
    });
}

const sellPriceInput = document.getElementById('sellPrice');
if (sellPriceInput) sellPriceInput.addEventListener('input', calculatePrices);
[document.getElementById('gstSelect'), document.getElementById('cessSelect'), document.getElementById('sellTaxType')].forEach(el => {
    if (el) el.addEventListener('change', calculatePrices);
});

// ==================
// New Category / Brand AJAX (PERSISTENT)
// ==================
const newCatForm = document.getElementById('newCategoryForm');
if(newCatForm) {
    newCatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('newCatName').value;
        const fd = new FormData();
        fd.append('name', name);
        fd.append('status', 'Active');
        fd.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('categories.store') }}", {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const sel = document.getElementById('categorySelect');
                const opt = new Option(name, name);
                sel.add(opt);
                sel.value = name;
                bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                newCatForm.reset();
            } else {
                alert(data.message || 'Error saving category');
            }
        });
    });
}

const newBrandForm = document.getElementById('newBrandForm');
if(newBrandForm) {
    newBrandForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('newBrandName').value;
        const fd = new FormData();
        fd.append('name', name);
        fd.append('status', 'Active');
        fd.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('brands.store') }}", {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const sel = document.getElementById('brandSelect');
                const opt = new Option(name, name);
                sel.add(opt);
                sel.value = name;
                bootstrap.Modal.getInstance(document.getElementById('brandModal')).hide();
                newBrandForm.reset();
            } else {
                alert(data.message || 'Error saving brand');
            }
        });
    });
}

// ==================
// Form Submit
// ==================
const productForm = document.getElementById('productForm');
if(productForm) {
    productForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        
        const btn = this.querySelector('.save-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        btn.disabled = true;

        fetch(this.action, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('successMsg').innerText = data.message;
                successModal.show();
                this.reset();
                if(document.getElementById('imagePreview')) document.getElementById('imagePreview').innerHTML = '';
                if(document.getElementById('unitConversionRows')) document.getElementById('unitConversionRows').innerHTML = '';
                setTimeout(() => successModal.hide(), 2000);
            } else {
                document.getElementById('errorMsg').innerText = data.message || 'Error occurred';
                errorModal.show();
            }
        })
        .catch(err => {
            document.getElementById('errorMsg').innerText = 'Server connection error';
            errorModal.show();
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
}

// Modal cleanup
successModalEl.addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
});

// Bulk form submit
const bulkForm = document.getElementById('bulkForm');
if(bulkForm){
    bulkForm.addEventListener('submit', function(e){
        e.preventDefault();
        const btn = document.getElementById('importBtn');
        const origText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Importing...';
        btn.disabled = true;

        const fd = new FormData(bulkForm);
        fetch(bulkForm.action, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                // Show result summary
                document.getElementById('resImported').innerText = data.imported ?? 0;
                document.getElementById('resUpdated').innerText  = data.updated  ?? 0;
                document.getElementById('resFailed').innerText   = data.failed   ?? 0;
                document.getElementById('importResult').style.display = 'block';

                // Show row errors if any
                const errBox  = document.getElementById('importErrors');
                const errList = document.getElementById('importErrorList');
                if(data.errors && data.errors.length > 0){
                    errList.innerHTML = data.errors.map(e => `<li>${e}</li>`).join('');
                    errBox.style.display = 'block';
                } else {
                    errBox.style.display = 'none';
                }

                document.getElementById('successMsg').innerText = data.message;
                successModal.show();
                bulkForm.reset();
                setTimeout(() => successModal.hide(), 2500);
            } else {
                document.getElementById('errorMsg').innerText = data.message || 'Import failed';
                errorModal.show();
            }
        })
        .catch(() => {
            document.getElementById('errorMsg').innerText = 'Server error during import';
            errorModal.show();
        })
        .finally(() => {
            btn.innerHTML = origText;
            btn.disabled = false;
        });
    });
}
</script>
@endpush

<!-- CATEGORY MODAL -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header">
        <h5 class="modal-title">Add New Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="newCategoryForm">
          <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" id="newCatName" class="form-control" required placeholder="e.g. Beverages">
          </div>
          <button type="submit" class="btn btn-primary w-100">Save Category</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- BRAND MODAL -->
<div class="modal fade" id="brandModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header">
        <h5 class="modal-title">Add New Brand</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="newBrandForm">
          <div class="mb-3">
            <label class="form-label">Brand Name</label>
            <input type="text" id="newBrandName" class="form-control" required placeholder="e.g. Nestle">
          </div>
          <button type="submit" class="btn btn-primary w-100">Save Brand</button>
        </form>
      </div>
    </div>
  </div>
</div>

</div>
@endsection
