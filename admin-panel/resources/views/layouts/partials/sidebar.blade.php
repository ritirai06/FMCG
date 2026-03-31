<button class="toggle-btn" type="button" onclick="toggleSidebar()">
  <i class="bi bi-list"></i>
</button>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    @if(admin_setting()?->profile_image)
      <img src="{{ asset('uploads/admin/'.admin_setting()->profile_image) }}"
           style="width:36px;height:36px;border-radius:8px;object-fit:cover;" alt="logo">
    @else
      <div class="sidebar-logo"><i class="bi bi-grid-fill"></i></div>
    @endif
    <div>
      <div class="sidebar-title">{{ admin_setting()?->company_name ?? 'Admin Panel' }}</div>
      <div class="sidebar-subtitle">
        @auth
          {{ ucfirst(auth()->user()->role) }} Panel
        @endauth
      </div>
    </div>
  </div>

  <nav class="sidebar-nav">

    {{-- ── ADMIN MENU ── --}}
    @if(auth()->user()?->role === 'admin')

      <a href="{{ route('dashboard') }}" class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>

      <span class="nav-label">Catalog</span>

      <div class="menu-item {{ request()->routeIs('product.*') ? 'open' : '' }}">
        <button class="menu-toggle {{ request()->routeIs('product.*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
          <i class="bi bi-box-seam"></i> <span>Products</span>
          <i class="bi bi-chevron-down submenu-arrow"></i>
        </button>
        <div class="submenu">
          <a href="{{ route('product.index') }}" class="{{ request()->routeIs('product.index') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> Product List</a>
          <a href="{{ route('product.create') }}" class="{{ request()->routeIs('product.create') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Add Product</a>
          <a href="{{ route('product.status') }}" class="{{ request()->routeIs('product.status') ? 'active' : '' }}"><i class="bi bi-toggle-on"></i> Product Status</a>
        </div>
      </div>

      <div class="menu-item {{ request()->routeIs('brands.*') ? 'open' : '' }}">
        <button class="menu-toggle {{ request()->routeIs('brands.*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
          <i class="bi bi-award"></i> <span>Brands</span>
          <i class="bi bi-chevron-down submenu-arrow"></i>
        </button>
        <div class="submenu">
          <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.index') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> Brands List</a>
          <a href="{{ route('brands.create') }}" class="{{ request()->routeIs('brands.create') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Add Brand</a>
          <a href="{{ route('brands.status') }}" class="{{ request()->routeIs('brands.status') ? 'active' : '' }}"><i class="bi bi-toggle-on"></i> Brand Status</a>
        </div>
      </div>

      <div class="menu-item {{ request()->routeIs('categories.*','subcategories.*') ? 'open' : '' }}">
        <button class="menu-toggle {{ request()->routeIs('categories.*','subcategories.*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
          <i class="bi bi-tags"></i> <span>Categories</span>
          <i class="bi bi-chevron-down submenu-arrow"></i>
        </button>
        <div class="submenu">
          <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.index') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> Categories</a>
          <a href="{{ route('categories.create') }}" class="{{ request()->routeIs('categories.create') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Add Category</a>
          <a href="{{ route('subcategories.index') }}" class="{{ request()->routeIs('subcategories.*') ? 'active' : '' }}"><i class="bi bi-list-nested"></i> SubCategories</a>
        </div>
      </div>

      <span class="nav-label">Operations</span>

      <div class="menu-item {{ request()->routeIs('warehouse.*') ? 'open' : '' }}">
        <button class="menu-toggle {{ request()->routeIs('warehouse.*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
          <i class="bi bi-building"></i> <span>Warehouses</span>
          <i class="bi bi-chevron-down submenu-arrow"></i>
        </button>
        <div class="submenu">
          <a href="{{ route('warehouse.index') }}" class="{{ request()->routeIs('warehouse.index') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> Warehouse List</a>
          <a href="{{ route('warehouse.create') }}" class="{{ request()->routeIs('warehouse.create') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Add Warehouse</a>
        </div>
      </div>

      <a href="{{ route('inventory.index') }}" class="nav-link-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
        <i class="bi bi-layers"></i> Inventory
      </a>

      <div class="menu-item {{ request()->routeIs('customers.*') ? 'open' : '' }}">
        <button class="menu-toggle {{ request()->routeIs('customers.*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
          <i class="bi bi-people"></i> <span>Customers</span>
          <i class="bi bi-chevron-down submenu-arrow"></i>
        </button>
        <div class="submenu">
          <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.index') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> Customer List</a>
          <a href="{{ route('customers.create') }}" class="{{ request()->routeIs('customers.create') ? 'active' : '' }}"><i class="bi bi-person-plus"></i> Add Customer</a>
        </div>
      </div>

      <a href="{{ route('store.store_index') }}" class="nav-link-item {{ request()->routeIs('store.*') ? 'active' : '' }}">
        <i class="bi bi-shop"></i> Parties / Stores
      </a>

      <a href="{{ route('orders.index') }}" class="nav-link-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i> Orders & Invoices
      </a>

      <span class="nav-label">People</span>

      <a href="{{ route('cities.index') }}" class="nav-link-item {{ request()->routeIs('cities.*') ? 'active' : '' }}">
        <i class="bi bi-geo-alt"></i> Cities & Localities
      </a>
      <a href="{{ route('sales.person.index') }}" class="nav-link-item {{ request()->routeIs('sales.person.*') ? 'active' : '' }}">
        <i class="bi bi-person-badge"></i> Sales Persons
      </a>
      <a href="{{ route('delivery.person.index') }}" class="nav-link-item {{ request()->routeIs('delivery.person.*') ? 'active' : '' }}">
        <i class="bi bi-truck"></i> Delivery Persons
      </a>
      <a href="{{ route('attendance.index') }}" class="nav-link-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i> Attendance
      </a>
      <a href="{{ route('salary.salary_index') }}" class="nav-link-item {{ request()->routeIs('salary.*') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i> Salary & Incentives
      </a>

      <span class="nav-label">Analytics</span>

      <a href="{{ route('report.report_index') }}" class="nav-link-item {{ request()->routeIs('report.*','reports.*') ? 'active' : '' }}">
        <i class="bi bi-graph-up"></i> Reports
      </a>
      <a href="{{ route('admin.settings') }}" class="nav-link-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <i class="bi bi-gear"></i> Settings
      </a>

      <span class="nav-label">System</span>

      <a href="{{ route('users.index') }}" class="nav-link-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> User Management
      </a>

      <hr class="sidebar-divider">

      <a href="{{ route('sale.login') }}" target="_blank" class="nav-link-item">
        <i class="bi bi-person-circle"></i> Sales Panel
      </a>
      <a href="{{ route('delivery.panel.login') }}" target="_blank" class="nav-link-item">
        <i class="bi bi-truck-front"></i> Delivery Panel
      </a>

    {{-- ── SALES MENU ── --}}
    @elseif(auth()->user()?->role === 'sales')

      <a href="{{ route('sale.panel.dashboard') }}" class="nav-link-item {{ request()->routeIs('sale.panel.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>

      <span class="nav-label">Sales</span>

      <a href="{{ route('sale.panel.parties') }}" class="nav-link-item {{ request()->routeIs('sale.panel.parties*') ? 'active' : '' }}">
        <i class="bi bi-shop"></i> Parties / Stores
      </a>
      <a href="{{ route('sale.panel.sale.new') }}" class="nav-link-item {{ request()->routeIs('sale.panel.sale.*') ? 'active' : '' }}">
        <i class="bi bi-plus-circle"></i> New Sale
      </a>
      <a href="{{ route('sale.panel.items') }}" class="nav-link-item {{ request()->routeIs('sale.panel.items*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Items
      </a>
      <a href="{{ route('sale.panel.payment.in') }}" class="nav-link-item {{ request()->routeIs('sale.panel.payment.*') ? 'active' : '' }}">
        <i class="bi bi-cash-coin"></i> Payment In
      </a>
      <a href="{{ route('sale.panel.transactions') }}" class="nav-link-item {{ request()->routeIs('sale.panel.transactions') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right"></i> Transactions
      </a>
      <a href="{{ route('sale.panel.returns') }}" class="nav-link-item {{ request()->routeIs('sale.panel.returns*') ? 'active' : '' }}">
        <i class="bi bi-arrow-return-left"></i> Returns
      </a>

      <span class="nav-label">My Activity</span>

      <a href="{{ route('sale.panel.attendance') }}" class="nav-link-item {{ request()->routeIs('sale.panel.attendance*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i> Attendance
      </a>
      <a href="{{ route('sale.panel.expenses') }}" class="nav-link-item {{ request()->routeIs('sale.panel.expenses*') ? 'active' : '' }}">
        <i class="bi bi-wallet2"></i> Expenses
      </a>
      <a href="{{ route('sale.panel.achievements') }}" class="nav-link-item {{ request()->routeIs('sale.panel.achievements') ? 'active' : '' }}">
        <i class="bi bi-trophy"></i> Achievements
      </a>

    {{-- ── DELIVERY MENU ── --}}
    @elseif(auth()->user()?->role === 'delivery')

      <a href="{{ route('delivery.panel.dashboard') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>

      <span class="nav-label">Deliveries</span>

      <a href="{{ route('delivery.panel.orders') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.orders') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i> My Orders
      </a>
      <a href="{{ route('delivery.panel.transactions') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.transactions*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> Transactions
      </a>
      <a href="{{ route('delivery.panel.items') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.items') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Items
      </a>
      <a href="{{ route('delivery.panel.stores') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.stores') ? 'active' : '' }}">
        <i class="bi bi-shop"></i> Stores
      </a>

      <span class="nav-label">My Activity</span>

      <a href="{{ route('delivery.panel.attendance') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.attendance') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i> Attendance
      </a>
      <a href="{{ route('delivery.panel.earnings') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.earnings') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i> Earnings
      </a>
      <a href="{{ route('delivery.panel.profile') }}" class="nav-link-item {{ request()->routeIs('delivery.panel.profile*') ? 'active' : '' }}">
        <i class="bi bi-person-circle"></i> Profile
      </a>

    @endif

    <hr class="sidebar-divider">

    <a href="{{ route('logout') }}" class="nav-link-item" style="color:rgba(255,255,255,.6);">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </nav>
</aside>
