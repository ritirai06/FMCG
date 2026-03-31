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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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

        #storeMap {
            height: 280px;
            border-radius: 10px;
            border: 1px solid #edf2f9;
        }

        .store-create-sections .card {
            height: auto !important;
            min-height: 0 !important;
        }

        .status-metric {
            border: 1px solid #edf2f9;
            border-radius: 12px;
            padding: 14px 16px;
            background: #f8fafc;
        }

        .status-metric .label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .status-metric .value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
        }

        .status-metric.total .value { color: #1f2937; }
        .status-metric.active .value { color: #16a34a; }
        .status-metric.inactive .value { color: #dc2626; }

        .modern-table {
            border: 1px solid #edf2f9;
            border-radius: 10px;
            overflow: hidden;
        }

        .modern-table table {
            margin-bottom: 0;
        }

        .modern-table thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 12px;
            color: #475569;
            border-bottom: 1px solid #edf2f9;
        }

        .modern-table td {
            font-size: 13px;
            vertical-align: middle;
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
                <h3 class="mb-0 me-auto">Create Store</h3>
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

            <div class="row store-create-sections">
                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Add Store</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('sale.store.store') }}" id="storeCreateForm">
                                @csrf
                                <input type="hidden" name="manager" value="{{ $salesPersonName }}">
                                <input type="hidden" name="status" value="1">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Store name <span class="text-danger">*</span></label>
                                        <input type="text" id="storeNameInput" name="store_name" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name') }}" placeholder="Enter store name" required>
                                        @error('store_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contact number <span class="text-danger">*</span></label>
                                        <input type="text" id="contactNumberInput" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" placeholder="Enter contact number" required>
                                        @error('contact_number') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea id="addressInput" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Enter full address" required>{{ old('address') }}</textarea>
                                        @error('address') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Assigned Locality</label>
                                        <select name="locality_name" id="localityName" class="form-control @error('locality_name') is-invalid @enderror">
                                            @php
                                                $oldLocality = old('locality_name');
                                            @endphp
                                            <option value="">Select locality</option>
                                            @forelse(($assignedLocalities ?? collect()) as $locality)
                                                <option value="{{ $locality->name }}"
                                                    @selected($oldLocality ? $oldLocality === $locality->name : $loop->first)>
                                                    {{ $locality->name }}
                                                </option>
                                            @empty
                                                <option value="">No assigned locality</option>
                                            @endforelse
                                        </select>
                                        @error('locality_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GPS Coordinates</label>
                                        <input type="text" id="gpsPreview" class="form-control" value="{{ old('latitude') && old('longitude') ? old('latitude') . ', ' . old('longitude') : 'Not captured yet' }}" readonly>
                                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Live GPS capture via map</label>
                                        <div id="storeMap"></div>
                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button" id="useCurrentLocationBtn" class="btn btn-sm btn-outline-primary">Use Current Location</button>
                                            <small class="text-muted align-self-center">Click on map to set exact store location.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('sale.store.list') }}" class="btn btn-danger light me-2 order-btn-stable">Cancel</a>
                                    <button type="submit" class="btn btn-primary order-btn-stable"><i class="fas fa-save me-2"></i>Save button</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Real Status</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <div class="status-metric total">
                                        <div class="label">Total Stores</div>
                                        <div class="value">{{ (int)($storeStats['total'] ?? 0) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="status-metric active">
                                        <div class="label">Active</div>
                                        <div class="value">{{ (int)($storeStats['active'] ?? 0) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="status-metric inactive">
                                        <div class="label">Inactive</div>
                                        <div class="value">{{ (int)($storeStats['inactive'] ?? 0) }}</div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mb-2">Recent Stores</h6>
                            <div class="modern-table">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 70%;">Store Name</th>
                                            <th style="width: 30%;">Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($recentStores ?? collect()) as $store)
                                            <tr>
                                                <td>{{ $store->store_name }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $store->code ?: '-' }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted text-center py-3">No recent stores</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-header border-0 pb-2">
                            <h4 class="card-title mb-0">Store Setup Checklist</h4>
                        </div>
                        <div class="card-body">
                            @php
                                $initLocality = old('locality_name');
                                if (!$initLocality && !collect($assignedLocalities ?? [])->isEmpty()) {
                                    $initLocality = collect($assignedLocalities)->first()->name;
                                }
                                $initGps = (old('latitude') && old('longitude')) ? (old('latitude') . ', ' . old('longitude')) : 'Not captured';
                            @endphp
                            <div class="modern-table">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Field</th>
                                            <th style="width: 70%;">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Add Store</strong></td>
                                            <td><span class="badge bg-success">Enabled</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Store name</strong></td>
                                            <td id="checklistStoreName" class="text-muted">{{ old('store_name') ?: 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address</strong></td>
                                            <td id="checklistAddress" class="text-muted">{{ old('address') ? \Illuminate\Support\Str::limit(old('address'), 45) : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contact number</strong></td>
                                            <td id="checklistContact" class="text-muted">{{ old('contact_number') ?: 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Locality</strong></td>
                                            <td id="checklistLocality" class="text-muted">{{ $initLocality ?: 'Not assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Assigned localities</strong></td>
                                            <td class="text-muted">{{ collect($assignedLocalities ?? [])->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>GPS</strong></td>
                                            <td id="checklistGps" class="text-muted">{{ $initGps }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function () {
        if (!window.L) return;

        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const gpsPreview = document.getElementById('gpsPreview');
        const currentLocationBtn = document.getElementById('useCurrentLocationBtn');
        const mapContainer = document.getElementById('storeMap');
        const storeNameInput = document.getElementById('storeNameInput');
        const contactNumberInput = document.getElementById('contactNumberInput');
        const addressInput = document.getElementById('addressInput');
        const localitySelect = document.getElementById('localityName');
        const checklistStoreName = document.getElementById('checklistStoreName');
        const checklistAddress = document.getElementById('checklistAddress');
        const checklistContact = document.getElementById('checklistContact');
        const checklistLocality = document.getElementById('checklistLocality');
        const checklistGps = document.getElementById('checklistGps');
        if (!mapContainer) return;

        const initialLat = parseFloat(latitudeInput?.value || '19.0760');
        const initialLng = parseFloat(longitudeInput?.value || '72.8777');
        const hasSavedCoords = !!(latitudeInput?.value && longitudeInput?.value);

        const map = L.map('storeMap').setView([initialLat, initialLng], hasSavedCoords ? 16 : 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker = null;

        function setCoordinates(lat, lng) {
            if (!latitudeInput || !longitudeInput || !gpsPreview) return;
            latitudeInput.value = Number(lat).toFixed(6);
            longitudeInput.value = Number(lng).toFixed(6);
            gpsPreview.value = latitudeInput.value + ', ' + longitudeInput.value;
            if (checklistGps) checklistGps.textContent = gpsPreview.value;
        }

        function setMarker(lat, lng, focus) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    setCoordinates(pos.lat, pos.lng);
                });
            }
            setCoordinates(lat, lng);
            if (focus) {
                map.setView([lat, lng], 16);
            }
        }

        if (hasSavedCoords) {
            setMarker(initialLat, initialLng, true);
        }

        map.on('click', function (e) {
            setMarker(e.latlng.lat, e.latlng.lng, false);
        });

        currentLocationBtn?.addEventListener('click', function () {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(function (position) {
                setMarker(position.coords.latitude, position.coords.longitude, true);
            });
        });

        function syncChecklist() {
            if (checklistStoreName) checklistStoreName.textContent = (storeNameInput?.value || '').trim() || 'Not set';
            if (checklistContact) checklistContact.textContent = (contactNumberInput?.value || '').trim() || 'Not set';
            if (checklistAddress) {
                const value = (addressInput?.value || '').trim();
                checklistAddress.textContent = value ? (value.length > 45 ? value.slice(0, 45) + '...' : value) : 'Not set';
            }
            if (checklistLocality) {
                const selected = localitySelect?.options?.[localitySelect.selectedIndex];
                checklistLocality.textContent = (selected?.value || '').trim() || 'Not assigned';
            }
            if (checklistGps && gpsPreview) {
                checklistGps.textContent = gpsPreview.value || 'Not captured';
            }
        }

        storeNameInput?.addEventListener('input', syncChecklist);
        contactNumberInput?.addEventListener('input', syncChecklist);
        addressInput?.addEventListener('input', syncChecklist);
        localitySelect?.addEventListener('change', syncChecklist);
        syncChecklist();
    })();
</script>
</body>
</html>

