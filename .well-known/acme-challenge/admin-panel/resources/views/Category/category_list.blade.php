
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Category List | FMCG Admin</title>

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

/* Modal */
.modal-content{border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,.15);}
.modal-header{border-bottom:none;}
.modal-footer{border-top:none;}
</style>
</head>
<body>

<!-- SIDEBAR -->
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
        <a href="product/product_margin.html"><i class="bi bi-cash-coin"></i> Auto Margin View</a>
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



<!-- CONTENT -->
<div class="content">
  <div class="topbar">
    <h5><i class="bi bi-list-ul me-2 text-primary"></i>Category List</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle me-1"></i>Add Category</button>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
@foreach($categories as $category)
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

  <td>{{ $category->description ?? '-' }}</td>

  <td>
    @if($category->status == 'Active')
      <span class="status-active">Active</span>
    @else
      <span class="status-inactive">Inactive</span>
    @endif
  </td>

  <td class="text-end">

    <!-- EDIT BUTTON -->
    <button class="btn btn-light btn-sm" data-edit-trigger="{{ $category->id }}"
      data-bs-toggle="modal"
      data-bs-target="#editModal{{ $category->id }}">
      <i class="bi bi-pencil text-primary"></i>
    </button>

    <!-- DELETE FORM -->
    <form class="delete-category-form" data-category-id="{{ $category->id }}"
          style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-light btn-sm delete-btn"
            onclick="deleteCategory(this)">
            <i class="bi bi-trash text-danger"></i>
        </button>
    </form>

  </td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

<!-- EDIT MODALS (outside table) -->
@foreach($categories as $category)
<div class="modal fade" id="editModal{{ $category->id }}">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-2">
      <form action="{{ route('categories.update',$category->id) }}"
            method="POST" data-edit-category="{{ $category->id }}">
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

<!-- ADD CATEGORY MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-2">
        <form id="addCategoryForm" action="{{ route('categories.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title text-primary"><i class="bi bi-plus-circle me-2"></i>Add Category</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Category Name</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Beverages" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="2" placeholder="Short description..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Category</button>
          </div>
        </form>
      </div>
  </div>
</div>

<!-- removed generic edit modal to avoid ID conflicts; per-row modals exist above -->
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}

// Delete category function
function deleteCategory(btn) {
  if (!confirm('Are you sure you want to delete this category?')) {
    return;
  }
  
  const form = btn.closest('form');
  const categoryId = form.getAttribute('data-category-id');
  const csrf = form.querySelector('input[name="_token"]').value;
  
  btn.disabled = true;
  
  fetch(`/categories/delete/${categoryId}`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: '_token=' + encodeURIComponent(csrf) + '&_method=DELETE'
  })
  .then(res => {
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  })
  .then(data => {
    if (data.success) {
      form.closest('tr').style.opacity = '0.5';
      setTimeout(() => {
        form.closest('tr').remove();
        // If no rows left, reload
        if (document.querySelectorAll('tbody tr').length === 0) {
          location.reload();
        }
      }, 300);
    } else {
      alert('Delete failed: ' + (data.message || 'Unknown error'));
      btn.disabled = false;
    }
  })
  .catch(err => {
    console.error(err);
    alert('Error: ' + err.message);
    btn.disabled = false;
  });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// AJAX submit for Add Category modal
document.addEventListener('DOMContentLoaded', function() {
  const addForm = document.getElementById('addCategoryForm');
  if (!addForm) return;

  addForm.addEventListener('submit', function(e){
    e.preventDefault();
    const btn = addForm.querySelector('button[type="submit"]');
    btn.disabled = true;

    const fd = new FormData(addForm);

    fetch(addForm.action, {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // close modal
        const modalEl = document.getElementById('addModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.hide();

        // append new row to table tbody
        const tbody = document.querySelector('table.table.align-middle tbody');
        if (tbody) {
          const c = data.category;
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="d-flex align-items-center gap-3">
              <img src="https://via.placeholder.com/45" class="category-img">
              <div>
                <strong>${escapeHtml(c.name)}</strong><br>
                <small class="text-muted">${escapeHtml(c.slug)}</small>
              </div>
            </td>
            <td>${escapeHtml(c.description || '-')}</td>
            <td>${c.status === 'Active' ? '<span class="status-active">Active</span>' : '<span class="status-inactive">Inactive</span>'}</td>
            <td class="text-end">
              <button class="btn btn-light btn-sm" data-edit-trigger="${c.id}" data-bs-toggle="modal" data-bs-target="#editModal${c.id}"><i class="bi bi-pencil text-primary"></i></button>
              <form action="/categories/delete/${c.id}" method="POST" style="display:inline-block;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-light btn-sm" onclick="return confirm('Delete this category?')"><i class="bi bi-trash text-danger"></i></button>
              </form>
            </td>
          `;
          tbody.prepend(tr);

          // Create and append edit modal for the new category
          const editModal = document.createElement('div');
          editModal.className = 'modal fade';
          editModal.id = 'editModal' + c.id;
          editModal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content p-2">
                <form action="/categories/${c.id}/update" method="POST" data-edit-category="${c.id}">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="_method" value="PUT">
                  <div class="modal-header">
                    <h5 class="modal-title text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Category</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label>Category Name</label>
                      <input type="text" name="name" value="${escapeHtml(c.name)}" class="form-control">
                    </div>
                    <div class="mb-3">
                      <label>Description</label>
                      <textarea name="description" class="form-control">${escapeHtml(c.description || '')}</textarea>
                    </div>
                    <div class="mb-3">
                      <label>Status</label>
                      <select name="status" class="form-select">
                        <option value="Active" ${c.status === 'Active' ? 'selected' : ''}>Active</option>
                        <option value="Inactive" ${c.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
                </form>
              </div>
            </div>
          `;
          document.body.appendChild(editModal);

          // Attach submit listener to new modal form
          const newForm = editModal.querySelector('form');
          attachEditFormListener(newForm);
        }

        addForm.reset();
      } else {
        alert('Insert failed');
      }
    })
    .catch(err => {
      console.error(err);
      alert('Server error');
    })
    .finally(() => btn.disabled = false);
  });
});

function escapeHtml(str){
  if(!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
</script>
<script>
// AJAX submit for Edit forms (per-row)
function attachEditFormListener(form) {
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const id = form.getAttribute('data-edit-category');
    const btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;

    const fd = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (data.success) {
        // hide modal
        const modalEl = document.getElementById('editModal' + id);
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.hide();
        
        // Reload page after successful update
        setTimeout(() => location.reload(), 500);
      } else {
        alert('Update failed: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(err => { 
      console.error(err); 
      alert('Error: ' + err.message); 
    })
    .finally(() => { if (btn) btn.disabled = false; });
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const editForms = document.querySelectorAll('form[data-edit-category]');
  editForms.forEach(form => attachEditFormListener(form));
});
</script>
</body>
</html>
