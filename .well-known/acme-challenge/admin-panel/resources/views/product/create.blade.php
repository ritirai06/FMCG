<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Product | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
    --primary:#1e3a8a;
    --accent:#3b82f6;
    --highlight:#60a5fa;
    --bg:#f1f5f9;
    --panel:#ffffff;
    --radius:18px;
    --text-dark:#0f172a;
    --text-muted:#6b7280;
    /* Sidebar gradient reused for branding on right side */
    --sidebar-grad: linear-gradient(180deg,#0f172a,#1e3a8a,#1d4ed8);
    --sidebar-grad-solid: #1e3a8a;
}

/* BODY */
body{
    font-family:'Inter',sans-serif;
    background:linear-gradient(135deg,#e0e7ff,#f9fafb);
    color:var(--text-dark);
    min-height:100vh;
    overflow-x:hidden;
}

/* SIDEBAR (MUST REMAIN EXACTLY AS-IS visually) */
.sidebar{
    position:fixed;
    top:0;left:0;height:100%;width:270px;
    background:linear-gradient(180deg,#0f172a,#1e3a8a,#1d4ed8);
    color:#fff;padding:28px 18px;
    box-shadow:0 8px 25px rgba(0,0,0,.4);
    overflow-y:auto;z-index:1000;
    transition:all .3s ease;
}
.sidebar::-webkit-scrollbar{width:6px;}
.sidebar::-webkit-scrollbar-thumb{background:#3b82f6;border-radius:6px;}

/* BRAND */
.brand{
    display:flex;align-items:center;gap:12px;
    font-size:20px;font-weight:800;margin-bottom:30px;
}
.brand i{
    width:46px;height:46px;border-radius:12px;
    background:rgba(255,255,255,.1);
    display:flex;align-items:center;justify-content:center;
    font-size:22px;color:#fff;
}

/* MENU */
.menu a{
    display:flex;align-items:center;gap:12px;
    color:rgba(255,255,255,.85);
    padding:10px 14px;
    border-radius:12px;
    text-decoration:none;
    font-weight:500;
    transition:.3s;
    margin-bottom:6px;
}
.menu a:hover{
    background:rgba(59,130,246,.15);
    transform:translateX(6px);
    color:#fff;
    box-shadow:inset 3px 0 0 var(--highlight);
}
.menu a.active{
    background:rgba(59,130,246,.25);
    box-shadow:inset 4px 0 0 var(--highlight);
    color:#fff;
}

/* SUBMENU */
.menu-item .submenu{
    display:none;
    flex-direction:column;
    margin-left:36px;
    padding-left:10px;
    border-left:2px solid rgba(255,255,255,.15);
    margin-top:4px;
}
.menu-item.open .submenu{display:flex;animation:fadeIn .3s ease;}
.submenu a{
    font-size:14px;
    color:rgba(255,255,255,.8);
    padding:8px 14px;
    border-radius:10px;
    margin-bottom:4px;
}
.submenu a:hover{background:rgba(59,130,246,.25);color:#fff;}

/* CONTENT (RIGHT SIDE ENHANCEMENTS) */
.content { margin-left: 240px; padding: 40px; display: flex; justify-content: center; }
.inner-content {
    width:100%;
    max-width:1200px;
    animation: fadeInUp .45s ease both;
}
@keyframes fadeInUp { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform: translateY(0);} }

/* Top area */
.topbar {
    background: var(--panel);
    border-radius: var(--radius);
    padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 8px 25px rgba(2,6,23,.1);
    width: 100%;
    margin-bottom: 18px;
}

/* Tabs: use sidebar gradient for active states */
.product-tabs { margin-bottom: 18px; }
.nav-tabs .nav-link {
    border: none;
    color: #475569;
    padding: 12px 18px;
    border-radius: 12px;
    margin-right: 6px;
    transition: all .22s ease;
    background: linear-gradient(90deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
    box-shadow: 0 6px 18px rgba(15,23,42,0.04);
}
.nav-tabs .nav-link.active {
    color: #fff;
    background: var(--sidebar-grad);
    box-shadow: 0 12px 30px rgba(2,6,23,0.18);
    transform: translateY(-4px);
    border-radius: 12px;
}

/* Section Card (refined) */
.section-card{
    background: linear-gradient(180deg, rgba(255,255,255,0.98), var(--panel));
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(2,6,23,0.06);
    transition: transform .22s ease, box-shadow .22s ease;
    border: 1px solid rgba(15,23,42,0.03);
    position:relative;
}
.section-card:hover{
    transform: translateY(-6px);
    box-shadow: 0 24px 60px rgba(2,6,23,0.08);
}
.section-head{
    display:flex;align-items:center;gap:12px;margin-bottom:12px;
}
.section-head .icon{
    width:44px;height:44px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    background: var(--sidebar-grad);
    color:#fff;font-size:18px;flex:0 0 44px;
    box-shadow:0 8px 20px rgba(2,6,23,0.12);
}
.section-title{font-weight:700;color:#0f172a;}
.section-sub{font-size:13px;color:var(--text-muted);margin-left:auto}

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

/* Sticky action bar */
.sticky-actions {
    position: sticky; bottom: 18px; z-index: 60;
    display:flex; justify-content:flex-end; gap:12px; align-items:center;
    background: transparent; margin-top:10px;
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
   
    <a href="{{ route('dashboard') }}" class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Products</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('product.index') }}"><i class="bi bi-list-ul"></i> Product List</a>
        <a href="{{ route('product.create') }}"><i class="bi bi-plus-circle"></i> Add Product</a>
        <a href="{{ route('product.status') }}"><i class="bi bi-toggle-on"></i> Product Status</a>
                <a href="#"><i class="bi bi-cash-coin"></i> Auto Margin View</a>
      </div>
    </div>
 
  <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Brands</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('brands.index') }}"><i class="bi bi-list-ul"></i> Brands List</a>
        <a href="{{ route('brands.create') }}"><i class="bi bi-plus-circle"></i> Add Brands</a>
        <a href="{{ route('brands.status') }}"><i class="bi bi-toggle-on"></i>Brands Status </a>
     
      </div>
    </div>
     <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Categories</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('categories.index') }}"><i class="bi bi-list-ul"></i> Categories List</a>
        <a href="{{ route('categories.create') }}"><i class="bi bi-plus-circle"></i> Add categories</a>
        <a href="{{ route('categories.status') }}"><i class="bi bi-toggle-on"></i> Categories Status</a>
        <a href="{{ route('subcategories.index') }}"><i class="bi bi-list-ul"></i> SubCategories</a>
        <a href="{{ route('subcategories.create') }}"><i class="bi bi-plus-circle"></i> Add SubCategory</a>
      </div>
    </div>
	 <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Warehouses</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('warehouse.create') }}"><i class="bi bi-list-ul"></i> Warehouses List</a>
        <a href="{{ route('warehouse.status') }}"><i class="bi bi-plus-circle"></i> Status Warehouses</a>
       
     
      </div>
    </div>
   
    <a href="{{ route('inventory.index') }}"><i class="bi bi-layers"></i><span>Inventory</span></a>
    <a href="{{ route('cities.index') }}"><i class="bi bi-geo-alt"></i><span>Cities & Localities</span></a>
    <a href="{{ route('sales.person.index') }}"><i class="bi bi-person-badge"></i><span>Sales Persons</span></a>
    <a href="{{ route('delivery.index') }}"><i class="bi bi-truck"></i><span>Delivery Persons</span></a>
    <a href="{{ route('attendance.index') }}"><i class="bi bi-calendar-check"></i><span>Attendance</span></a>
    <a href="{{ route('salary.salary_index') }}"><i class="bi bi-cash-stack"></i><span>Salary & Incentives</span></a>
    <a href="{{ route('store.store_index') }}"><i class="bi bi-shop"></i><span>Stores</span></a>
    <a href="{{ route('order_management.order_index') }}"><i class="bi bi-receipt"></i><span>Orders & Invoices</span></a>
    <a href="{{ route('report.report_index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
    <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
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

        <!-- Tabs -->
        <div class="product-tabs">
            <ul class="nav nav-tabs" id="productModeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab" aria-controls="single" aria-selected="true">
                        <i class="bi bi-person-fill me-2"></i> Single Product Entry
                    </button>
                </li>
               <!-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab" aria-controls="bulk" aria-selected="false">
                        <i class="bi bi-upload me-2"></i> Bulk Product Upload
                    </button>
                </li>-->
            </ul>
        </div>

        <div class="tab-content">

            <!-- TAB 1: SINGLE PRODUCT ENTRY -->
            <div class="tab-pane fade show active" id="single" role="tabpanel" aria-labelledby="single-tab">

             <div class="tab-pane fade show active" id="single">

<form id="productForm"
      action="{{ route('product.store') }}"
      method="POST"
      enctype="multipart/form-data">
@csrf

<div id="formAlerts" class="mb-3"></div>

<div class="section-card">
  <div class="section-head">
    <div class="icon"><i class="bi bi-box"></i></div>
    <div>
      <div class="section-title">Product Details</div>
      <div class="small-muted">Single product entry</div>
    </div>
  </div>

  <div class="row g-3">

    <!-- Product Name -->
    <div class="col-md-6">
      <label class="form-label">Product Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <!-- SKU -->
    <div class="col-md-6">
      <label class="form-label">SKU / Product Code</label>
      <input type="text" name="sku" class="form-control" required>
    </div>

    <!-- Brand -->
    <div class="col-md-4">
      <label class="form-label">Brand</label>
      <select id="brandSelect" name="brand" class="form-select" required>
        <option value="">Select Brand</option>
        @forelse($brands as $brand)
          <option value="{{ $brand->name }}">{{ $brand->name }}</option>
        @empty
          <option value="">No brands found</option>
        @endforelse
      </select>
    </div>

    <!-- Category -->
    <div class="col-md-4">
      <label class="form-label">Category</label>
      <select id="categorySelect" name="category" class="form-select" required>
        <option value="">Select Category</option>
        @forelse($categories as $category)
          <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
        @empty
          <option value="">No categories found</option>
        @endforelse
      </select>
    </div>

    <!-- Sub Category -->
    <div class="col-md-4">
      <label class="form-label">Sub-Category</label>
      <select id="subCategorySelect" name="sub_category" class="form-select">
        <option value="">Optional</option>
        @foreach($subCategories as $subCategory)
          <option value="{{ $subCategory->name }}" data-parent-id="{{ $subCategory->parent_id }}">{{ $subCategory->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Purchase Price -->
    <div class="col-md-3">
      <label class="form-label">Purchase Price (₹)</label>
      <input type="number" id="purchasePrice"
             name="purchase_price"
             class="form-control" step="0.01" required>
    </div>

    <!-- Sale Price -->
    <div class="col-md-3">
      <label class="form-label">Sale Price (₹)</label>
      <input type="number" id="salePrice"
             name="sale_price"
             class="form-control" step="0.01" required>
    </div>

    <!-- Margin -->
    <div class="col-md-3">
      <label class="form-label">Margin (₹)</label>
      <input type="text" id="marginAmount"
             class="form-control bg-light"
             readonly>
    </div>

    <!-- MRP -->
    <div class="col-md-3">
      <label class="form-label">MRP (₹)</label>
      <input type="number"
             name="mrp"
             class="form-control" step="0.01" required>
    </div>

    <!-- Status -->
    <div class="col-md-6">
      <label class="form-label">Product Status</label>
      <select name="status" class="form-select">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>
    </div>

    <!-- Image -->
    <div class="col-md-6">
      <label class="form-label">Product Image</label>
      <input type="file"
             name="image"
             class="form-control"
             accept="image/*" required>
    </div>

  </div>
</div>

<div class="sticky-actions">
  <button type="submit" class="btn btn-primary save-btn">
    <i class="bi bi-check-circle me-2"></i>Save Product
  </button>
</div>

</form>
</div>

            </div>

            <!-- TAB 2: BULK UPLOAD 
            <div class="tab-pane fade" id="bulk" role="tabpanel" aria-labelledby="bulk-tab">
                <div class="section-card bulk-card">
                    <div class="section-head">
                        <div class="icon"><i class="bi bi-upload"></i></div>
                        <div>
                            <div class="section-title">Bulk Product Upload</div>
                            <div class="small-muted">Upload multiple products via CSV / XLSX</div>
                        </div>
                        <div class="section-sub">CSV / XLSX</div>
                    </div>

                    <div id="bulkAlerts" class="mb-3"></div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <div id="bulkUploader" class="uploader" role="button" aria-label="Upload bulk CSV or Excel">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-file-earmark-arrow-up fs-1 text-primary"></i>
                                    <div class="mt-2 fw-semibold">Drop CSV / XLSX here or click to upload</div>
                                    <div class="small-muted mt-1">Supported: .csv (preview), .xlsx (uploaded). Max 10,000 rows</div>
                                </div>
                                <input id="bulkFileInput" type="file" accept=".csv,.xlsx" class="d-none">
                            </div>
                            <div class="small-muted mt-2">Tip: Use the sample template for correct column order.</div>
                        </div>

                        <div class="col-md-5 d-flex flex-column justify-content-between">
                            <div class="d-flex gap-2 bulk-actions">
                                <button id="downloadTemplate" class="btn btn-outline-primary"><i class="bi bi-download"></i> Download Sample Template</button>
                                <button id="validateBtn" class="btn btn-outline-secondary"><i class="bi bi-check2-square"></i> Validate File</button>
                                <button id="clearBulkBtn" class="btn btn-outline-danger"><i class="bi bi-x-circle"></i> Reset</button>
                            </div>

                            <div class="mt-3">
                                <div class="small-muted">Validation Checklist</div>
                                <ul id="validationChecklist" class="small-muted mt-2" style="line-height:1.6">
                                    <li id="vc1">• File selected</li>
                                    <li id="vc2">• Required columns present</li>
                                    <li id="vc3">• Duplicate SKU check</li>
                                    <li id="vc4">• Numeric values valid</li>
                                </ul>
                                <div class="mt-2">
                                    <span id="errorCountBadge" class="badge bg-danger d-none">0 errors</span>
                                    <span id="warningCountBadge" class="badge bg-warning text-dark d-none">0 warnings</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="preview-wrapper mt-2">
                                <table id="previewTable" class="preview-table table table-sm mb-0">
                                    <thead class="table-light"><tr id="previewHead"></tr></thead>
                                    <tbody id="previewBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                            <div style="flex:1" class="align-self-center small-muted">Preview shows up to 500 rows. Highlighted rows contain errors.</div>
                            <div class="d-flex gap-2">
                                <button id="bulkCancel" class="btn btn-outline-secondary">Cancel</button>
                                <button id="bulkUploadBtn" class="btn btn-primary"><i class="bi bi-cloud-upload me-1"></i> Upload Products</button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="progress mt-2 d-none" id="bulkProgressWrapper" aria-hidden="true">
                                <div class="progress-bar" role="progressbar" style="width:0%" id="bulkProgressBar">0%</div>
                            </div>
                            <div id="bulkSummary" class="mt-2 small-muted"></div>
                        </div>

                        <div class="col-12">
                            <div id="bulkResults" class="bulk-results"></div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>-->
</div>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

 <script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>

<script>
// ==================
// Margin Calculation
// ==================
const purchase = document.getElementById('purchasePrice');
const sale = document.getElementById('salePrice');
const margin = document.getElementById('marginAmount');

function calculateMargin(){
  const p = parseFloat(purchase.value) || 0;
  const s = parseFloat(sale.value) || 0;
  margin.value = (s - p).toFixed(2);
}

purchase.addEventListener('input', calculateMargin);
sale.addEventListener('input', calculateMargin);


// ==================
// Bootstrap Modals (ONE TIME)
// ==================
const successModalEl = document.getElementById('successModal');
const errorModalEl   = document.getElementById('errorModal');

const successModal = new bootstrap.Modal(successModalEl);
const errorModal   = new bootstrap.Modal(errorModalEl);

// ==================
// Dynamic Category -> SubCategory
// ==================
const categorySelect = document.getElementById('categorySelect');
const subCategorySelect = document.getElementById('subCategorySelect');
const allSubCategoryOptions = subCategorySelect ? Array.from(subCategorySelect.querySelectorAll('option[data-parent-id]')) : [];

function filterSubCategoriesByCategory() {
  if (!categorySelect || !subCategorySelect) return;

  const selectedOption = categorySelect.options[categorySelect.selectedIndex];
  const selectedCategoryId = selectedOption ? selectedOption.getAttribute('data-id') : null;

  subCategorySelect.innerHTML = '<option value="">Optional</option>';

  allSubCategoryOptions
    .filter((opt) => String(opt.getAttribute('data-parent-id')) === String(selectedCategoryId))
    .forEach((opt) => subCategorySelect.appendChild(opt.cloneNode(true)));
}

if (categorySelect && subCategorySelect) {
  categorySelect.addEventListener('change', filterSubCategoriesByCategory);
  filterSubCategoriesByCategory();
}


// ==================
// Product Form Submit
// ==================
const productForm = document.getElementById('productForm');

productForm.addEventListener('submit', function(e){
  e.preventDefault();

  if(!productForm.checkValidity()){
    productForm.classList.add('was-validated');
    return;
  }

  const fd = new FormData(productForm);

  fetch(productForm.action, {
    method: 'POST',
    body: fd,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(async (res) => {
    const contentType = res.headers.get('content-type') || '';
    let payload = {};
    if (contentType.includes('application/json')) {
      payload = await res.json();
    } else {
      const text = await res.text();
      payload = { success: false, message: text || 'Unexpected server response' };
    }
    if (!res.ok) {
      payload.success = false;
      if (!payload.message && payload.errors) {
        payload.message = Object.values(payload.errors).flat().join('\n');
      }
    }
    return payload;
  })
  .then(data => {

    if(data.success === true){

      document.getElementById('successMsg').innerText = data.message;
      successModal.show();

      productForm.reset();
      margin.value = '';

    } else {

      let errMsg = data.message || 'Insert failed';
      if (data.errors) {
        errMsg = Object.values(data.errors).flat().join('\n');
      }
      document.getElementById('errorMsg').innerText = errMsg;
      errorModal.show();
    }

  })
  .catch(() => {
    document.getElementById('errorMsg').innerText = 'Server error';
    errorModal.show();
  });
});


// ==================
// SAFETY: Remove stuck backdrop (optional but recommended)
// ==================
successModalEl.addEventListener('hidden.bs.modal', () => {
  document.body.classList.remove('modal-open');
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
}); 

</script>

<script>
document.getElementById('successModal')
  .addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop')
      .forEach(el => el.remove());
});
</script>

</body>
</html>
