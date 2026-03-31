@php
  $pageTitle = trim($__env->yieldContent('page_title'));
  if ($pageTitle === '') {
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Dashboard';
  }
@endphp

<div class="topbar">
  <div class="topbar-left">
    <button class="toggle-btn" type="button" onclick="toggleSidebar()" aria-label="Toggle menu">
      <i class="bi bi-list"></i>
    </button>
    <h5 class="mb-0">{{ $pageTitle }}</h5>
  </div>

  <div class="topbar-right">
    @hasSection('navbar_right')
      @yield('navbar_right')
    @else
      <div class="user-panel d-none d-sm-flex">
        <div class="user-avatar"><i class="bi bi-person"></i></div>
        <div>
          <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
          <div class="user-email">{{ auth()->user()->email ?? '' }}</div>
        </div>
      </div>
      <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger d-none d-md-inline-flex align-items-center gap-1">
        <i class="bi bi-box-arrow-right"></i><span>Logout</span>
      </a>
    @endif
  </div>
</div>
