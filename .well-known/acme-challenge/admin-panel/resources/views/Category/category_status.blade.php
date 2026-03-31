<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Category Status | FMCG Admin</title>

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
}

/* BODY */
body{
  font-family:'Inter',sans-serif;
  background:linear-gradient(135deg,#e0e7ff,#f9fafb);
  color:var(--text-dark);
  min-height:100vh;
  overflow-x:hidden;
}

/* SIDEBAR */
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

/* CONTENT */
.content{
  margin-left:270px;
  padding:30px;
  background:var(--bg);
  min-height:100vh;
  transition:.4s;
}
@media(max-width:992px){
  .sidebar{left:-100%;}
  .sidebar.show{left:0;}
  .content{margin-left:0;}
  .toggle-btn{
    position:fixed;top:18px;left:18px;
    background:var(--primary);color:#fff;border:none;
    border-radius:10px;width:44px;height:44px;font-size:22px;z-index:2000;
  }
}

/* Content */
.content{margin-left:260px;padding:30px;}
.topbar{
  background:var(--panel);border-radius:var(--radius);
  padding:16px 24px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);
}

/* Table Card */
.table-card{
  background:var(--panel);
  border-radius:var(--radius);
  box-shadow:0 15px 40px rgba(0,0,0,.06);
  margin-top:24px;
  overflow:hidden;
  backdrop-filter:blur(10px);
  padding:20px;
}
.table thead{background:rgba(99,102,241,0.08);}
.table tbody tr:hover{background:rgba(99,102,241,.06);transform:scale(1.01);transition:0.3s;}
.category-img{width:45px;height:45px;border-radius:12px;object-fit:cover;}
.status-active{color:#22c55e;font-weight:600;}
.status-inactive{color:#ef4444;font-weight:600;}

/* Buttons */
.btn-primary{background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;}
.btn-primary:hover{background:linear-gradient(135deg,#4f46e5,#7c3aed);}
.btn-light{background:#f1f5f9;border:none;}

/* Stats Cards */
.stats-container{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-top:24px;
}
.stat-card{
  background:var(--panel);
  border-radius:var(--radius);
  padding:20px;
  box-shadow:0 10px 40px rgba(0,0,0,.06);
  text-align:center;
}
.stat-card h6{color:var(--text-muted);font-size:12px;font-weight:600;margin-bottom:10px;}
.stat-card h3{color:var(--primary);font-weight:700;margin:0;}
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
        <a href="{{ route('categories.status') }}" class="active"><i class="bi bi-toggle-on"></i> Categories Status</a>
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

<!-- CONTENT -->
<div class="content">
  <div class="topbar">
    <h5><i class="bi bi-toggle-on me-2 text-primary"></i>Category Status</h5>
    <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to List</a>
  </div>

  <!-- STATS CARDS -->
  <div class="stats-container">
    <div class="stat-card">
      <h6>TOTAL CATEGORIES</h6>
      <h3>{{ count($categories) }}</h3>
    </div>
    <div class="stat-card">
      <h6 style="color:#22c55e;">ACTIVE CATEGORIES</h6>
      <h3 style="color:#22c55e;">{{ count($categories->where('status','Active')) }}</h3>
    </div>
    <div class="stat-card">
      <h6 style="color:#ef4444;">INACTIVE CATEGORIES</h6>
      <h3 style="color:#ef4444;">{{ count($categories->where('status','Inactive')) }}</h3>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created Date</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
@forelse($categories as $category)
<tr>
  <td class="d-flex align-items-center gap-3">
    @if($category->image)
      <img src="{{ asset('storage/'.$category->image) }}" class="category-img">
    @else
      <img src="https://via.placeholder.com/45" class="category-img">
    @endif
    <div>
      <strong>{{ $category->name }}</strong><br>
      <small class="text-muted">{{ $category->slug }}</small>
    </div>
  </td>
  <td>{{ Str::limit($category->description ?? '-', 50) }}</td>
  <td>
    <select class="form-select form-select-sm status-select" data-category-id="{{ $category->id }}" style="max-width:140px;">
      <option value="Active" {{ $category->status == 'Active' ? 'selected' : '' }}>
        <i class="bi bi-check-circle"></i> Active
      </option>
      <option value="Inactive" {{ $category->status == 'Inactive' ? 'selected' : '' }}>
        <i class="bi bi-x-circle"></i> Inactive
      </option>
    </select>
  </td>
  <td>
    <small class="text-muted">{{ $category->created_at->format('M d, Y') }}</small>
  </td>
  <td class="text-end">
    <button class="btn btn-light btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}" title="Edit">
      <i class="bi bi-pencil text-primary"></i>
    </button>
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted py-4">
    <i class="bi bi-inbox" style="font-size:32px;"></i><br>
    No categories found
  </td>
</tr>
@endforelse
</tbody>
      </table>
    </div>
  </div>

  <!-- EDIT MODALS -->
  @foreach($categories as $category)
  <div class="modal fade" id="editModal{{ $category->id }}">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-2">
        <form class="edit-category-form" data-edit-category="{{ $category->id }}">
          @csrf
          @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title text-primary">
              <i class="bi bi-pencil-square me-2"></i>Edit Category
            </h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label>Category Name</label>
              <input type="text"
                     name="name"
                     value="{{ $category->name }}"
                     class="form-control">
            </div>

            <div class="mb-3">
              <label>Description</label>
              <textarea name="description"
                        class="form-control">{{ $category->description }}</textarea>
            </div>

            <div class="mb-3">
              <label>Status</label>
              <select name="status" class="form-select">
                <option value="Active"
                  {{ $category->status=='Active'?'selected':'' }}>
                  Active
                </option>
                <option value="Inactive"
                  {{ $category->status=='Inactive'?'selected':'' }}>
                  Inactive
                </option>
              </select>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-light"
                    data-bs-dismiss="modal">
                    Cancel
            </button>
            <button type="submit" class="btn btn-primary">
                Update
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
  @endforeach

</div>

<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}

// Status dropdown change handler
document.addEventListener('DOMContentLoaded', function() {
  const statusSelects = document.querySelectorAll('.status-select');
  
  statusSelects.forEach(select => {
    select.addEventListener('change', function(e) {
      const categoryId = this.getAttribute('data-category-id');
      const newStatus = this.value;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value;
      
      const btn = this;
      btn.disabled = true;
      
      fetch(`/categories/update/${categoryId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `status=${encodeURIComponent(newStatus)}&_method=PUT`
      })
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(data => {
        if (data.success) {
          // Update the UI immediately
          const tr = btn.closest('tr');
          const statusCell = tr.querySelector('td:nth-child(3)');
          if (statusCell) {
            statusCell.innerHTML = `
              <select class="form-select form-select-sm status-select" data-category-id="${categoryId}" style="max-width:140px;">
                <option value="Active" ${newStatus === 'Active' ? 'selected' : ''}>
                  <i class="bi bi-check-circle"></i> Active
                </option>
                <option value="Inactive" ${newStatus === 'Inactive' ? 'selected' : ''}>
                  <i class="bi bi-x-circle"></i> Inactive
                </option>
              </select>
            `;
            // Re-attach listener to new select
            const newSelect = statusCell.querySelector('.status-select');
            newSelect.addEventListener('change', arguments.callee);
          }
        } else {
          alert('Update failed: ' + (data.message || 'Unknown error'));
          // Revert on failure
          location.reload();
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error: ' + err.message);
        location.reload();
      });
    });
  });
  
  // Edit form submission
  const editForms = document.querySelectorAll('.edit-category-form');
  editForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const id = this.getAttribute('data-edit-category');
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value;
      
      const btn = this.querySelector('button[type="submit"]');
      btn.disabled = true;
      
      const fd = new FormData(this);
      
      fetch(`/categories/update/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: fd
      })
      .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then(data => {
        if (data.success) {
          // Close modal
          const modalEl = document.getElementById('editModal' + id);
          const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
          modal.hide();
          
          // Reload page to show updated data
          setTimeout(() => location.reload(), 300);
        } else {
          alert('Update failed: ' + (data.message || 'Unknown error'));
          btn.disabled = false;
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error: ' + err.message);
        btn.disabled = false;
      });
    });
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
