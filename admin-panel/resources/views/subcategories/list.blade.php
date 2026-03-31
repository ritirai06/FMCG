@extends('layouts.app')

@section('title', 'SubCategory List')

@push('styles')
<style>
/* Page-specific styles */
.table-card{
  background:var(--panel, #fff);
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
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-list-ul me-2 text-primary"></i>SubCategory List</h5>
    <a href="{{ route('subcategories.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add SubCategory</a>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>SubCategory</th>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($subcategories as $sub)
          <tr>
            <td class="d-flex align-items-center gap-3">
              @if($sub->image)
                <img src="{{ asset('uploads/subcategories/'.$sub->image) }}" class="category-img">
              @else
                <img src="https://via.placeholder.com/45" class="category-img">
              @endif
              <div>
                <strong>{{ $sub->name }}</strong><br>
                <small class="text-muted">{{ $sub->slug }}</small>
              </div>
            </td>
            <td>{{ $sub->category->name ?? '-' }}</td>
            <td>{{ $sub->description ?? '-' }}</td>
            <td>
              @if($sub->status == 'Active')
                <span class="status-active">Active</span>
              @else
                <span class="status-inactive">Inactive</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editSub{{ $sub->id }}">
                <i class="bi bi-pencil text-primary"></i>
              </button>
              <form action="{{ route('subcategories.delete',$sub->id) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-light btn-sm" onclick="return confirm('Delete this subcategory?')">
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
  @foreach($subcategories as $sub)
  <div class="modal fade" id="editSub{{ $sub->id }}">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-2">
        <form action="{{ route('subcategories.update',$sub->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title text-primary"><i class="bi bi-pencil-square me-2"></i>Edit SubCategory</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label>SubCategory Name</label>
              <input type="text" name="name" value="{{ $sub->name }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Category</label>
              <select name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ $sub->category_id==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label>Description</label>
              <textarea name="description" class="form-control">{{ $sub->description }}</textarea>
            </div>
            <div class="mb-3">
              <label>Image (leave blank to keep)</label>
              <input type="file" name="image" class="form-control">
            </div>
            <div class="mb-3">
              <label>Status</label>
              <select name="status" class="form-select">
                <option value="Active" {{ $sub->status=='Active'?'selected':'' }}>Active</option>
                <option value="Inactive" {{ $sub->status=='Inactive'?'selected':'' }}>Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endforeach
@endsection
