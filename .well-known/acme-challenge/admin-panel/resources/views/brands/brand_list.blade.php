
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Brand Management | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

/* TOPBAR */
.topbar{
  background:var(--panel);
  border-radius:var(--radius);
  padding:20px 28px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  box-shadow:0 10px 25px rgba(0,0,0,.05);
  margin-bottom:24px;
}
.topbar h5{
  font-weight:700;
  display:flex;align-items:center;gap:10px;
}
.topbar .btn{
  background:var(--primary);
  border:none;
}
.topbar .btn:hover{background:#1d4ed8;}

/* TABLE */
.table thead{background:rgba(99,102,241,0.08);}
.table tbody tr:hover{background:rgba(99,102,241,0.05);transition:.3s;}
.brand-logo{
  width:45px;height:45px;border-radius:50%;object-fit:cover;
  box-shadow:0 3px 6px rgba(0,0,0,0.15);
}
.status-dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px;}
.status-active{background:#22c55e;}
.status-inactive{background:#ef4444;}

/* MODALS */
.modal-content{
  border-radius:20px;
  background:rgba(255,255,255,0.95);
  backdrop-filter:blur(12px);
  box-shadow:0 15px 50px rgba(0,0,0,0.2);
  border:none;
}
.modal-dialog.small{max-width:400px;}
.confirm-icon{font-size:48px;display:flex;justify-content:center;margin-bottom:15px;}
.confirm-icon .bi-exclamation-triangle{color:#ef4444;text-shadow:0 0 15px rgba(239,68,68,0.6);}
.confirm-icon .bi-check-circle{color:#22c55e;text-shadow:0 0 15px rgba(34,197,94,0.6);}

/* FORM STYLE */
.brand-form input,.brand-form textarea,.brand-form select{
  border-radius:10px;border:1px solid #e2e8f0;font-size:14px;
}
.logo-upload{
  text-align:center;border:2px dashed #cbd5e1;padding:15px;border-radius:12px;
  cursor:pointer;transition:all 0.3s ease;
}
.logo-upload:hover{background:rgba(99,102,241,0.05);border-color:var(--primary);}
.logo-upload img{
  width:70px;height:70px;border-radius:50%;object-fit:cover;
  box-shadow:0 2px 6px rgba(0,0,0,0.2);
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


<!-- TOGGLE BUTTON -->
<button class="toggle-btn d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="bi bi-list"></i></button>

<!-- MAIN CONTENT -->
<div class="content">
  <div class="topbar">
    <h5><i class="bi bi-shop me-2 text-primary"></i>Brand Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal">
      <i class="bi bi-plus-circle me-1"></i>Add Brand
    </button>
  </div>

  <!-- TABLE -->
  <div class="mt-4 bg-white rounded-4 shadow-sm p-4">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr><th>Brand</th><th>Description</th><th>Status</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
@foreach($brands as $brand)
<tr id="row-{{ $brand->id }}">

<td class="d-flex align-items-center gap-3">
    <img src="{{ $brand->logo ? asset('storage/'.$brand->logo) : 'https://via.placeholder.com/45' }}" 
         class="brand-logo">
    <div>
        <div class="fw-semibold">{{ $brand->name }}</div>
        <small class="text-muted">{{ $brand->description }}</small>
    </div>
</td>

<td>{{ $brand->description }}</td>

<td>
    <span class="status-dot {{ $brand->status=='Active' ? 'status-active':'status-inactive' }}"></span>
    {{ $brand->status }}
</td>

<td class="text-end">

    <button class="btn btn-light btn-sm editBtn"
        data-id="{{ $brand->id }}">
        <i class="bi bi-pencil text-primary"></i>
    </button>

    <button class="btn btn-light btn-sm deleteBtn"
        data-id="{{ $brand->id }}">
        <i class="bi bi-trash text-danger"></i>
    </button>

</td>

</tr>
@endforeach
</tbody>

      </table>
    </div>
  </div>
</div>

<!-- ADD/EDIT BRAND MODAL -->
<div class="modal fade" id="brandModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered small">
    <div class="modal-content p-3">
      <h5 class="fw-bold mb-3" id="modalTitle">Add Brand</h5>
      <form class="brand-form">
        <div class="logo-upload mb-3" id="logoUpload">
          <img id="logoPreview" src="https://via.placeholder.com/70" alt="Brand Logo">
          <div class="small text-muted mt-2">Click to upload logo</div>
          <input type="file" id="logoInput" hidden accept="image/*">
        </div>
        <div class="mb-2">
          <label class="form-label small">Brand Name</label>
          <input type="text" class="form-control" id="brandName" placeholder="Enter brand name">
        </div>
        <div class="mb-2">
          <label class="form-label small">Description</label>
          <textarea class="form-control" id="brandDesc" rows="2" placeholder="Enter description"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label small">Status</label>
          <select class="form-select" id="brandStatus">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CONFIRMATION MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered small">
    <div class="modal-content text-center p-3">
      <div class="confirm-icon" id="confirmIcon"></div>
      <h5 class="fw-bold mb-2" id="confirmTitle">Deactivate Brand?</h5>
      <p class="text-muted small" id="confirmText">Are you sure you want to deactivate this brand?</p>
      <div class="d-flex justify-content-center gap-2 mt-3">
        <button class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger px-3" id="confirmBtn">Confirm</button>
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
const brandModal = new bootstrap.Modal(document.getElementById('brandModal'));
const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

let currentBrandId = null;

// ================= EDIT =================
document.querySelectorAll('.editBtn').forEach(btn=>{
    btn.addEventListener('click', function(){

        currentBrandId = this.dataset.id;

        fetch(`/brands/${currentBrandId}/edit`)
        .then(res=>res.json())
        .then(data=>{
            document.getElementById('modalTitle').innerText="Edit Brand";
            document.getElementById('brandName').value=data.name;
            document.getElementById('brandDesc').value=data.description;
            document.getElementById('brandStatus').value=data.status;
            brandModal.show();
        });
    });
});

// ================= SAVE (ADD / UPDATE) =================
document.querySelector('.brand-form').addEventListener('submit', function(e){
    e.preventDefault();

    let name = document.getElementById('brandName').value;
    let desc = document.getElementById('brandDesc').value;
    let status = document.getElementById('brandStatus').value;

    if(currentBrandId){ 
        // UPDATE
        fetch(`/brands/update/${currentBrandId}`,{
            method:"POST",
            headers:{
                "X-CSRF-TOKEN":"{{ csrf_token() }}",
                "Content-Type":"application/json"
            },
            body: JSON.stringify({name:name,description:desc,status:status})
        })
        .then(res=>res.json())
        .then(()=>{
            location.reload();
        });

    } else {
        // ADD
        fetch("{{ route('brands.store') }}",{
            method:"POST",
            headers:{
                "X-CSRF-TOKEN":"{{ csrf_token() }}",
                "Content-Type":"application/json"
            },
            body: JSON.stringify({name:name,description:desc,status:status})
        })
        .then(res=>res.json())
        .then(()=>{
            location.reload();
        });
    }
});

// ================= DELETE =================
document.querySelectorAll('.deleteBtn').forEach(btn=>{
    btn.addEventListener('click', function(){
        currentBrandId = this.dataset.id;
        confirmModal.show();
    });
});

document.getElementById('confirmBtn').addEventListener('click', function(){

    fetch(`/brands/${currentBrandId}`,{
        method:"DELETE",
        headers:{
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        }
    })
    .then(res=>res.json())
    .then(()=>{
        document.getElementById(`row-${currentBrandId}`).remove();
        confirmModal.hide();
    });
});
</script>

</body>
</html>
