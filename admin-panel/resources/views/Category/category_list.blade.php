@extends('layouts.app')

@section('title', 'Category List | FMCG Admin')

@push('styles')
<style>
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
.table tbody tr:hover{background:rgba(99,102,241,.06);transform:scale(1.005);transition:0.3s;}
.category-img{width:45px;height:45px;border-radius:12px;object-fit:cover;}
.status-active{color:#22c55e;font-weight:600;}
.status-inactive{color:#ef4444;font-weight:600;}

/* Buttons */
.btn-primary{background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;}
.btn-primary:hover{background:linear-gradient(135deg,#4f46e5,#7c3aed);}

/* Modal */
.modal-content{border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,.15); background: rgba(255,255,255,0.98); backdrop-filter: blur(10px);}
.modal-header{border-bottom:none;}
.modal-footer{border-top:none;}
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-list-ul me-2 text-primary"></i>Category List</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-circle me-1"></i>Add Category</button>
  </div>

  <div class="table-card bg-white">
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
      <img src="{{ asset('uploads/categories/'.$category->image) }}" class="category-img">
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

<!-- EDIT MODALS -->
@foreach($categories as $category)
<div class="modal fade" id="editModal{{ $category->id }}">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-2">
      <form action="{{ route('categories.update',$category->id) }}"
            method="POST" data-edit-category="{{ $category->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Category</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Category Name</label>
            <input type="text" name="name" value="{{ $category->name }}" class="form-control">
          </div>
          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $category->description }}</textarea>
          </div>
          <div class="mb-3">
            <label>Upload Image</label>
            @if($category->image)
              <div class="mb-2">
                <img src="{{ asset('uploads/categories/'.$category->image) }}" width="60" height="60" style="border-radius:8px;object-fit:cover;">
                <small class="d-block text-muted">Current Image</small>
              </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/png,image/jpg,image/jpeg">
            <small class="text-muted">Optional. Leave empty to keep current image</small>
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
              <option value="Active" {{ $category->status=='Active'?'selected':'' }}>Active</option>
              <option value="Inactive" {{ $category->status=='Inactive'?'selected':'' }}>Inactive</option>
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
</div>
@endforeach

<!-- ADD CATEGORY MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-2">
        <form id="addCategoryForm" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
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
              <label class="form-label">Upload Image</label>
              <input type="file" name="image" class="form-control" accept="image/png,image/jpg,image/jpeg">
              <small class="text-muted">Optional. Accepts PNG, JPG, JPEG (Max 2MB)</small>
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
@endsection

@push('scripts')
<script>
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

function escapeHtml(str){
  if(!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

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
        const modalEl = document.getElementById('editModal' + id);
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.hide();
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
  const addForm = document.getElementById('addCategoryForm');
  if (addForm) {
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
          location.reload();
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
  }

  const editForms = document.querySelectorAll('form[data-edit-category]');
  editForms.forEach(form => attachEditFormListener(form));
});
</script>
@endpush
