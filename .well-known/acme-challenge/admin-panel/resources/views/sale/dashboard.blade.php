
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- PAGE TITLE HERE -->
	<title>Jobick: Job Admin Dashboard Bootstrap 5 Template + FrontEnd</title>

    <!-- Meta -->
	<meta charset="utf-8">
	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Favicon icon -->
	<link rel="shortcut icon" type="image/png" href="{{ asset('sale_assets/images/favicon.png') }}">

	<!-- All StyleSheet -->
	<link href="{{ asset('sale_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
	<link href="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
	<link href="{{ asset('sale_assets/vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">

	<!-- Localization Tool (if present) -->
	<link href="{{ asset('sale_assets/css/jquery.localizationTool.css') }}" rel="stylesheet">

	<!-- Main Style Css -->
	<link class="main-css" href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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

        		<!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="/sale/dashboard" class="brand-logo">
				<img class="logo-abbr" src="{{ $companyLogo }}" alt="{{ $companyDisplayName }}" style="width:42px;height:42px;object-fit:cover;border-radius:10px;">
				<span class="brand-title" style="font-size: 14px; font-weight: 600; color: #464646; margin-left: 8px;">{{ $companyDisplayName }}</span>

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
								<div class="dashboard_bar">
					{{ $companyDisplayName }} Dashboard
					</div>
								<div class="nav-item d-flex align-items-center">
									<form action="index.html">
										<div class="input-group search-area">
											<input type="text" class="form-control" placeholder="Search">
											<span class="input-group-text"><button type="submit" class="btn"><i class="flaticon-381-search-2"></i></button></span>
										</div>
									</form>
									<div class="plus-icon">
										<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"><i class="fas fa-plus"></i></a>
									</div>
								</div>
				</div>
																												<ul class="navbar-nav header-right">
								<li class="nav-item dropdown notification_dropdown">
									<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24">
											<g data-name="Layer 2" transform="translate(-2 -2)">
												<path id="Path_20" data-name="Path 20" d="M22.571,15.8V13.066a8.5,8.5,0,0,0-7.714-8.455V2.857a.857.857,0,0,0-1.714,0V4.611a8.5,8.5,0,0,0-7.714,8.455V15.8A4.293,4.293,0,0,0,2,20a2.574,2.574,0,0,0,2.571,2.571H9.8a4.286,4.286,0,0,0,8.4,0h5.23A2.574,2.574,0,0,0,26,20,4.293,4.293,0,0,0,22.571,15.8ZM7.143,13.066a6.789,6.789,0,0,1,6.78-6.78h.154a6.789,6.789,0,0,1,6.78,6.78v2.649H7.143ZM14,24.286a2.567,2.567,0,0,1-2.413-1.714h4.827A2.567,2.567,0,0,1,14,24.286Zm9.429-3.429H4.571A.858.858,0,0,1,3.714,20a2.574,2.574,0,0,1,2.571-2.571H21.714A2.574,2.574,0,0,1,24.286,20a.858.858,0,0,1-.857.857Z"></path>
											</g>
										</svg>
										<span class="badge light text-white bg-primary rounded-circle">9</span>
									</a>
									<div class="dropdown-menu dropdown-menu-end">
										<div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:380px;">
											<ul class="timeline">
												<li><div class="timeline-panel"><div class="media me-2"><span class="badge bg-primary text-white">Order</span></div><div class="media-body"><h6 class="mb-1">New Order Placed - Rs 3,450</h6><small class="d-block">2 min ago</small></div></div></li>
												<li><div class="timeline-panel"><div class="media me-2"><span class="badge bg-success text-white">Delivered</span></div><div class="media-body"><h6 class="mb-1">Order Delivered - Sharma Store</h6><small class="d-block">20 min ago</small></div></div></li>
												<li><div class="timeline-panel"><div class="media me-2"><span class="badge bg-danger text-white">Return</span></div><div class="media-body"><h6 class="mb-1">Order Returned - #1009</h6><small class="d-block">45 min ago</small></div></div></li>
											</ul>
										</div>
										<a class="all-notification" href="javascript:void(0);">See all notifications</a>
									</div>
								</li>
								<li class="nav-item dropdown notification_dropdown">
									<a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
										<svg xmlns="http://www.w3.org/2000/svg" width="23.262" height="24" viewbox="0 0 23.262 24">
											<g id="icon" transform="translate(-1565 90)">
												<path id="setting_1_" data-name="setting (1)" d="M30.45,13.908l-1-.822a1.406,1.406,0,0,1,0-2.171l1-.822a1.869,1.869,0,0,0,.432-2.385L28.911,4.293a1.869,1.869,0,0,0-2.282-.818l-1.211.454a1.406,1.406,0,0,1-1.88-1.086l-.213-1.276A1.869,1.869,0,0,0,21.475,0H17.533a1.869,1.869,0,0,0-1.849,1.567L15.47,2.842a1.406,1.406,0,0,1-1.88,1.086l-1.211-.454a1.869,1.869,0,0,0-2.282.818L8.126,7.707a1.869,1.869,0,0,0,.432,2.385l1,.822a1.406,1.406,0,0,1,0,2.171l-1,.822a1.869,1.869,0,0,0-.432,2.385L10.1,19.707a1.869,1.869,0,0,0,2.282.818l1.211-.454a1.406,1.406,0,0,1,1.88,1.086l.213,1.276A1.869,1.869,0,0,0,17.533,24h3.943a1.869,1.869,0,0,0,1.849-1.567l.213-1.276a1.406,1.406,0,0,1,1.88-1.086l1.211.454a1.869,1.869,0,0,0,2.282-.818l1.972-3.415a1.869,1.869,0,0,0-.432-2.385ZM27.287,18.77l-1.211-.454a3.281,3.281,0,0,0-4.388,2.533l-.213,1.276H17.533l-.213-1.276a3.281,3.281,0,0,0-4.388-2.533l-1.211.454L9.75,15.355l1-.822a3.281,3.281,0,0,0,0-5.067l-1-.822L11.721,5.23l1.211.454A3.281,3.281,0,0,0,17.32,3.151l.213-1.276h3.943l.213,1.276a3.281,3.281,0,0,0,4.388,2.533l1.211-.454,1.972,3.414h0l-1,.822a3.281,3.281,0,0,0,0,5.067l1,.822ZM19.5,7.375A4.625,4.625,0,1,0,24.129,12,4.63,4.63,0,0,0,19.5,7.375Zm0,7.375A2.75,2.75,0,1,1,22.254,12,2.753,2.753,0,0,1,19.5,14.75Z" transform="translate(1557.127 -90)"></path>
											</g>
										</svg>
										<span class="badge light text-white bg-primary rounded-circle">5</span>
									</a>
									<div class="dropdown-menu dropdown-menu-end">
										<div id="DZ_W_TimeLine02" class="widget-timeline dlab-scroll style-1 p-3 height370">
											<ul class="timeline">
												<li><div class="timeline-badge primary"></div><a class="timeline-panel text-muted" href="javascript:void(0);"><span>10 minutes ago</span><h6 class="mb-0">Order Created - Rs 2,150</h6></a></li>
												<li><div class="timeline-badge info"></div><a class="timeline-panel text-muted" href="javascript:void(0);"><span>30 minutes ago</span><h6 class="mb-0">Store Visit Completed - Sharma Store</h6></a></li>
												<li><div class="timeline-badge success"></div><a class="timeline-panel text-muted" href="javascript:void(0);"><span>1 hour ago</span><h6 class="mb-0">Order Delivered - #1015</h6></a></li>
											</ul>
										</div>
									</div>
								</li>
								<li class="nav-item dropdown header-profile">
									<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
										<img src="{{ $salesProfileImage }}" width="20" alt="{{ $salesPersonName }}">
									</a>
									<div class="dropdown-menu dropdown-menu-end">
										<a href="/sale/profile" class="dropdown-item ai-icon">
											<svg id="icon-user2" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
											<span class="ms-2">{{ $salesPersonName }}</span>
										</a>
										<a href="/sale/attendance" class="dropdown-item ai-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
											<span class="ms-2">{{ $salesPersonStatus }}</span>
										</a>
										<a href="/sale/logout" class="dropdown-item ai-icon" onclick="return confirm('Are you sure you want to logout?')">
											<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
											<span class="ms-2">Logout</span>
										</a>
									</div>
								</li>
							</ul>
					</div>
				</nav>
				</div>
			</div>
		<!--**********************************
			Header end ti-comment-alt
        ***********************************-->
		
				<!--**********************************
            Sidebar start
        ***********************************-->
		<div class="dlabnav">
            <div class="dlabnav-scroll">
				<div class="dropdown header-profile2 ">
					<a class="nav-link " href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
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
					<div class="dropdown-menu dropdown-menu-end">
						<a href="/sale/profile" class="dropdown-item ai-icon ">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
							<span class="ms-2">Profile </span>
						</a>
						<a href="/sale/dashboard" class="dropdown-item ai-icon">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
							<span class="ms-2">Dashboard </span>
						</a>
						<a href="/sale/logout" class="dropdown-item ai-icon" onclick="return confirm('Are you sure you want to logout?')">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
							<span class="ms-2">Logout </span>
						</a>
					</div>
				</div>
				<ul class="metismenu" id="menu">
					<li><a href="/sale/dashboard">
							<i class="flaticon-025-dashboard"></i>
							<span class="nav-text">Dashboard</span>
						</a>
					</li>
						<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
								<i class="flaticon-381-user-7"></i>
								<span class="nav-text">Orders</span>
							</a>
							<ul aria-expanded="false">
								<li><a href="/sale/order/create">Create Order</a></li>
								<li><a href="/sale/order/list">Order List</a></li>
							</ul>
						</li>
						<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
								<i class="flaticon-093-waving"></i>
								<span class="nav-text">Stores</span>
							</a>
							<ul aria-expanded="false">
								<li><a href="/sale/store/create">Create Store</a></li>
								<li><a href="/sale/store/list">Store List</a></li>
							</ul>
						</li>
					<li><a href="/sale/attendance">
							<i class="flaticon-381-user-4"></i>
							<span class="nav-text">Attendance</span>
						</a>
					</li>
					<li><a href="/sale/profile">
							<i class="flaticon-381-internet"></i>
							<span class="nav-text">Profile</span>
						</a>
					</li>
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
        <!--**********************************
            Sidebar end
        ***********************************-->
		
		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="row">
					<div class="col-xl-6">
						<div class="row">
							<div class="col-xl-12">
	<div class="card">
		<div class="card-body">
			<div class="row separate-row">

				<!-- KPI CARDS (dynamic) -->
				<div class="col-sm-6">
					<div class="job-icon pb-4 d-flex justify-content-between">
						<div>
							<div class="d-flex align-items-center mb-1">
								<h2 class="mb-0 lh-1">{{ $todayOrders ?? 0 }}</h2>
							</div>
							<span class="d-block mb-2">Today Orders</span>
						</div>
						<div id="NewCustomers"></div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="job-icon pb-4 pt-4 pt-sm-0 d-flex justify-content-between">
						<div>
							<div class="d-flex align-items-center mb-1">
								<h2 class="mb-0 lh-1">&#8377;{{ number_format($todayRevenue ?? 0) }}</h2>
							</div>
							<span class="d-block mb-2">Today Revenue</span>
						</div>
						<div id="NewCustomers1"></div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="job-icon pt-4 pb-sm-0 pb-4 d-flex justify-content-between">
						<div>
							<div class="d-flex align-items-center mb-1">
								<h2 class="mb-0 lh-1">{{ $totalProducts ?? 0 }}</h2>
							</div>
							<span class="d-block mb-2">Total Products</span>
						</div>
						<div id="NewCustomers2"></div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="job-icon pt-4 d-flex justify-content-between">
						<div>
							<div class="d-flex align-items-center mb-1">
								<h2 class="mb-0 lh-1 text-danger">{{ $lowStock ?? 0 }}</h2>
							</div>
							<span class="d-block mb-2">Low Stock Items</span>
						</div>
						<div id="NewCustomers3"></div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

							<div class="col-xl-12">
	<div class="card" id="user-activity">
		<div class="card-header border-0 pb-0 flex-wrap">
			<h4 class="card-title mb-0">Sales Performance</h4>
			<div class="mt-3 mt-sm-0">
				<ul class="nav nav-tabs vacany-tabs style-1" role="tablist">
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" data-series="Daily" href="#Daily" role="tab">Daily</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-bs-toggle="tab" data-series="Weekly" href="#Weekly" role="tab">Weekly</a>
					</li>
					<li class="nav-item">
						<a class="nav-link active" data-bs-toggle="tab" data-series="Monthly" href="#Monthly" role="tab">Monthly</a>
					</li>
				</ul>
			</div>
		</div>

		<div class="card-body pt-3 px-sm-3 px-0 pb-1">

			<!-- LEGENDS -->
			<div class="pb-sm-4 mb-3 d-flex flex-wrap px-3">

				<!-- TOTAL SALES -->
				<div class="d-flex align-items-center">
					<svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" rx="6.5" fill="#35c556"></rect>
					</svg>
					<span class="text-dark fs-13 font-w500">Total Sales</span>
				</div>

				<!-- ORDERS PLACED -->
				<div class="application d-flex align-items-center">
					<svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" rx="6.5" fill="#3f4cfe"></rect>
					</svg>
					<span class="text-dark fs-13 font-w500">Orders Placed</span>
				</div>

				<!-- ORDERS RETURNED -->
				<div class="application d-flex align-items-center">
					<svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" rx="6.5" fill="#f34040"></rect>
					</svg>
					<span class="text-dark fs-13 font-w500">Orders Returned</span>
				</div>

			</div>

			<!-- CHART -->
			<div>
				<div id="vacancyChart" class="ltr"></div>
			</div>

		</div>
	</div>
</div>

							<div class="col-xl-12">
	<div class="card" id="user-activity1">
		<div class="card-header border-0 pb-0">
			<h4 class="card-title mb-0">Order Activity</h4>
			<ul class="nav nav-tabs style-1 chart-tab" role="tablist">
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" data-series="Daily" href="#Daily1" role="tab">Daily</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" data-series="Weekly" href="#Weekly1" role="tab">Weekly</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" data-bs-toggle="tab" data-series="Monthly" href="#Monthly1" role="tab">Monthly</a>
				</li>
			</ul>
		</div>

		<div class="card-body ps-sm-3 ps-0 pb-2">

			<!-- SUMMARY ROW -->
			<div class="d-sm-flex d-block mb-3 mx-3">

				<!-- ORDERS DELIVERED -->
				<div class="d-flex align-items-center me-5 mb-sm-0 mb-2">
					<svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" fill="#f73a0b"></rect>
					</svg>
					<label class="form-label mb-0 me-4">Orders Delivered</label>
					<h6 class="mb-0 me-1" id="ordersDeliveredCount">{{ $monthlyDelivered }}</h6>
					<span class="text-success fs-13 font-w500">+4%</span>
				</div>

				<!-- SALES VALUE -->
				<div class="d-flex align-items-center">
					<svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" fill="#6e6e6e"></rect>
					</svg>
					<label class="form-label mb-0 me-4">Sales Value</label>
					<h6 class="mb-0 me-1" id="salesValueAmount">&#8377;{{ number_format($monthlySalesValue) }}</h6>
				</div>

			</div>

			<!-- CHART -->
			<div>
				<div id="activity1" class="ltr"></div>
			</div>

		</div>
	</div>
</div>

							<div class="col-xl-12">
								<div class="card">
									<div class="card-header border-0">
										<h4 class="card-title mb-1">Assigned Stores</h4>
										<div class="dropdown">
											<a href="javascript:void(0);" class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
												<svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
													<path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
													<path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
												</svg>
											</a>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="javascript:void(0);">Delete</a>
												<a class="dropdown-item" href="javascript:void(0);">Edit</a>
											</div>
										</div>
									</div>
									<div class="card-body pt-0 loadmore-content dlab-scroll height370 " id="scroll-y">
										<div class="row list-grid-area" id="FeaturedCompaniesContent">
											<div class="d-flex align-items-center list-item-bx">
													<div class="col-xl-6 col-sm-6">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
															  <g transform="translate(-457 -443)">
																<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
																<g transform="translate(457 443)">
																  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#2769ee"></rect>
																  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
																  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
																</g>
															  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">Sharma General Store</h5>
														<span>Andheri - Mumbai</span>
													</div>
												</div>
											</div>
											<div class="col-xl-6 col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
															  <g transform="translate(-457 -443)">
																<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
																<g transform="translate(457 443)">
																  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#7630d2"></rect>
																  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
																  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
																</g>
															  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">City Mart</h5>
							<span>Ghatkopar - Mumbai</span>
													</div>
												</div>
											</div>
											<div class="col-xl-6  col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
															  <g transform="translate(-457 -443)">
																<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
																<g transform="translate(457 443)">
																  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#b848ef"></rect>
																  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
																  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
																</g>
															  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">Fresh Basket</h5>
							<span>Kanjurmarg - Mumbai</span>
													</div>
												</div>
											</div>
											<div class="col-xl-6 col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
															  <g transform="translate(-457 -443)">
																<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
																<g transform="translate(457 443)">
																  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#7e1d74"></rect>
																  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
																  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
																</g>
															  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">Daily Needs Store</h5>
							<span>Bhandup - Mumbai</span>

													</div>
												</div>
											</div>
											<div class="col-xl-6 col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
															  <g transform="translate(-457 -443)">
																<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
																<g transform="translate(457 443)">
																  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#0411c2"></rect>
																  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
																  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
																</g>
															  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">Omah Ku Inc.</h5>
														<span>Desgin Team Agency</span>
													</div>
												</div>
											</div>
											<div class="col-xl-6 col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
															  <g transform="translate(-457 -443)">
																<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
																<g transform="translate(457 443)">
																  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#378a82"></rect>
																  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
																  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
																</g>
															  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">Ventic</h5>
														<span>Desgin Team Agency</span>
													</div>
												</div>
											</div>
											<div class="col-xl-6 col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
															  <g transform="translate(-457 -443)">
																<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
																<g transform="translate(457 443)">
																  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#175baa"></rect>
																  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
																  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
																</g>
															  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">Sakola</h5>
														<span>Desgin Team Agency</span>
													</div>
												</div>
											</div>
											<div class="col-xl-6 col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
													<div class="icon-img-bx">
														<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
														  <g transform="translate(-457 -443)">
															<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
															<g transform="translate(457 443)">
															  <rect data-name="placeholder" width="71" height="71" rx="12" fill="#eeb927"></rect>
															  <circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
															  <circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
															</g>
														  </g>
														</svg>
													</div>
													<div class="ms-3 featured">
														<h5 class="mb-1">Uena Foods</h5>
														<span>Desgin Team Agency</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="card-footer border-0 m-auto pt-3">
										<a href="javascript:void(0);" class="btn btn-outline-primary m-auto dlab-load-more" id="FeaturedCompanies" rel="ajax/featuredcompanies.html">View more</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-6">
						<div class="row">
							<div class="col-xl-12">
	<div class="card">
		<div class="card-body">
			<div class="row ">
				<div class="col-xl-8 col-xxl-7 col-sm-7">
					<div class="update-profile d-flex">
						<img src="{{ $salesProfileImage }}" alt="">
						<div class="ms-4">
							<h3 class="mb-0">{{ $salesPersonName }}</h3>
							<span class="text-primary d-block mb-xl-3 mb-1">{{ $salesPersonStatus }}</span>
							<span><i class="fas fa-map-marker-alt me-1"></i>{{ $salesPerson?->city ?? 'Location N/A' }}</span>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-xxl-5 col-sm-5 sm-mt-auto mt-3 text-sm-end">
					<a href="javascript:void(0);" class="btn btn-primary">Update Profile</a>
				</div>
			</div>

			<div class="row mt-4 align-items-center">
				<h4 class="fs-20 mb-0 mt-1">Sales Summary</h4>

				<div class="col-xl-6 col-sm-6">

					<div class="progress default-progress">
						<div class="progress-bar bg-green progress-animated" style="width: 100%; height:8px;" role="progressbar">
							<span class="sr-only">100% Complete</span>
						</div>
					</div>
					<div class="d-flex align-items-end mt-2 pb-4 justify-content-between">
						<span class="font-w500">Today&rsquo;s Order Count</span>
						<h6 class="mb-0">{{ (int) ($todayOrders ?? 0) }}</h6>
					</div>

					<div class="progress default-progress">
						<div class="progress-bar bg-info progress-animated" style="width: 100%; height:8px;" role="progressbar">
							<span class="sr-only">100% Complete</span>
						</div>
					</div>
					<div class="d-flex align-items-end mt-2 pb-4 justify-content-between">
						<span class="font-w500">Today&rsquo;s Sales Value</span>
						<h6 class="mb-0">&#8377;{{ number_format((float) ($todayRevenue ?? 0), 2) }}</h6>
					</div>

					<div class="progress default-progress">
						<div class="progress-bar bg-blue progress-animated" style="width: 100%; height:8px;" role="progressbar">
							<span class="sr-only">100% Complete</span>
						</div>
					</div>
					<div class="d-flex align-items-end mt-2 justify-content-between">
						<span class="font-w500">Monthly Sales Summary</span>
						<h6 class="mb-0">&#8377;{{ number_format((float) ($monthlySalesSummary ?? 0), 2) }}</h6>
					</div>

				</div>

				</div>
			</div>
		</div>
</div>

							<div class="col-xl-12">
								<div class="card">
									<div class="card-header border-0 pb-2">
										<h4 class="card-title mb-0">Recent Sales Activity</h4>
										<div>	
											<select class="default-select dashboard-select">
											 <option data-display="Newest">Newest</option>
												<option value="1">Latest</option>
												<option value="2">Oldest</option>
											</select>
											<div class="dropdown custom-dropdown mb-0">
												<div class="btn sharp tp-btn dark-btn" data-bs-toggle="dropdown">
													<svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
														<path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
														<path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
													</svg>
												</div>
												<div class="dropdown-menu dropdown-menu-right">
													<a class="dropdown-item" href="javascript:void(0);">Details</a>
													<a class="dropdown-item text-danger" href="javascript:void(0);">Cancel</a>
												</div>
											</div>
										</div>	
									</div>
									<div class="card-body loadmore-content pb-0 pt-3 list-grid-area dlab-scroll height370 recent-activity-wrapper" id="RecentActivityContent">
										@forelse($recentOrders as $order)
										<div class="d-flex recent-activity">
											<span class="me-3 activity">
												<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewbox="0 0 17 17">
												  <circle cx="8.5" cy="8.5" r="8.5" fill="@if(strpos($order->status, 'Delivered') !== false || strpos($order->status, 'Delivery') !== false || strpos($order->status, 'Completed') !== false) #28a745 @elseif(strpos($order->status, 'Pending') !== false) #ffc107 @else #dc3545 @endif"></circle>
												</svg>
											</span>
											<div class="d-flex align-items-center list-item-bx">
												<div class="icon-img-bx">
													<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
														<g transform="translate(-457 -443)">
															<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
															<g transform="translate(457 443)">
															<rect data-name="placeholder" width="71" height="71" rx="12" fill="@if(strpos($order->status, 'Delivered') !== false || strpos($order->status, 'Delivery') !== false || strpos($order->status, 'Completed') !== false) #22bc32 @elseif(strpos($order->status, 'Pending') !== false) #eeac27 @else #dc3545 @endif"></rect>
															<circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
															<circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
															</g>
														</g>
													</svg>
												</div>
												<div class="ms-3">
													<h6 class="mb-1">Order #{{ $order->id }} - {{ $order->store?->store_name ?? 'Unknown Store' }} - {{ $order->status }}</h6>
													<p class="mb-0">&#8377;{{ number_format($order->amount ?? 0) }} - {{ $order->created_at?->diffForHumans() ?? 'Just now' }}</p>
												</div>
											</div>
										</div>
										@empty
										<div class="text-center p-3"><p class="text-muted">No recent orders</p></div>
										@endforelse
									</div>
									<div class="card-footer border-0 text-center">
										<a href="javascript:void(0);" class="btn btn-outline-primary m-auto dlab-load-more" id="RecentActivity" rel="ajax/recentactivity.html">View more</a>
									</div>
								</div>
							</div>
<div class="col-xl-12">
	<div class="card">
		<div class="card-header pb-0 border-0 flex-wrap">
			<h4 class="card-title mb-sm-0 mb-2">Assigned Stores & Tasks</h4>
			<div>	
				<select class="default-select dashboard-select">
					<option data-display="Newest">Newest</option>
					<option value="2">Oldest</option>
				</select>
				<div class="dropdown custom-dropdown mb-0">
					<div class="btn sharp tp-btn dark-btn" data-bs-toggle="dropdown">
						<svg width="24" height="24" viewBox="0 0 24 24"></svg>
					</div>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="javascript:void(0);">Details</a>
						<a class="dropdown-item text-danger" href="javascript:void(0);">Cancel</a>
					</div>
				</div>
			</div>	
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th><strong>Store Name</strong></th>
							<th><strong>Code</strong></th>
							<th><strong>Manager</strong></th>
							<th><strong>Location</strong></th>
							<th><strong>Pending Tasks</strong></th>
							<th><strong>Delivered</strong></th>
							<th><strong>Monthly Revenue</strong></th>
						</tr>
					</thead>
					<tbody>
						@forelse($assignedStores as $store)
						<tr>
							<td>
								<h6 class="mb-0">{{ $store->store_name }}</h6>
							</td>
							<td>
								<span class="badge badge-primary">{{ $store->code ?? 'N/A' }}</span>
							</td>
							<td>{{ $store->manager ?? 'N/A' }}</td>
							<td>
								<small class="text-muted">{{ $store->address ?? 'N/A' }}</small>
							</td>
							<td>
								@if($store->pending_orders > 0)
									<span class="badge badge-warning">{{ $store->pending_orders }} Pending</span>
								@else
									<span class="badge badge-success">No Pending</span>
								@endif
							</td>
							<td>
								<span class="badge badge-success">{{ $store->delivered_orders }}</span>
							</td>
							<td>
								<strong>&#8377;{{ number_format($store->monthly_revenue, 0) }}</strong>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="7" class="text-center text-muted py-4">
								<p>No assigned stores yet</p>
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

							<div class="col-xl-12">
	<div class="card">
		<div class="card-header border-0 pb-0">
			<h4 class="card-title">Sales Coverage</h4>
			<div class="dropdown custom-dropdown mb-0">
				<div class="btn sharp tp-btn dark-btn" data-bs-toggle="dropdown">
					<svg width="24" height="24" viewBox="0 0 24 24"></svg>
				</div>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" href="javascript:void(0);">Details</a>
					<a class="dropdown-item text-danger" href="javascript:void(0);">Cancel</a>
				</div>
			</div>
		</div>

		<div class="card-body pb-2">
			<div class="row sp10">

				<div class="col-xl-3 col-md-3 col-6 mb-4 text-center">
					<div class="d-inline-block ms-auto me-auto mb-md-3 mb-2 db-donut-chart-sale me-4">
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>{{ $retailFraction }}</span>
						<h4 class="mb-0 pie-label">{{ $retailCoverage }}%</h4>
					</div>
					<h5 class="mb-1">Retail Stores</h5>
					<p class="mb-0">{{ $retailStores }} Covered</p>
				</div>

				<div class="col-xl-3 col-md-3 col-6 mb-4 text-center">
					<div class="d-inline-block ms-auto me-auto mb-md-3 mb-2 db-donut-chart-sale me-4">
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>{{ $wholesaleFraction }}</span>
						<h4 class="mb-0 pie-label">{{ $wholesaleCoverage }}%</h4>
					</div>
					<h5 class="mb-1">Wholesale Clients</h5>
					<p class="mb-0">{{ $wholesaleCount }} Active</p>
				</div>

				<div class="col-xl-3 col-md-3 col-6 mb-4 text-center">
					<div class="d-inline-block ms-auto me-auto mb-md-3 mb-2 db-donut-chart-sale me-4">
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>{{ $deliveryFraction }}</span>
						<h4 class="mb-0 pie-label">{{ $deliveryCompletion }}%</h4>
					</div>
					<h5 class="mb-1">Orders Delivered</h5>
					<p class="mb-0">{{ $ordersDeliveredThisMonth }} Completed</p>
				</div>

				<div class="col-xl-3 col-md-3 col-6 mb-4 text-center">
					<div class="d-inline-block ms-auto me-auto mb-md-3 mb-2 db-donut-chart-sale me-4">
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>{{ $salesTargetPercentage }}/100</span>
						<h4 class="mb-0 pie-label">{{ $salesTargetPercentage }}%</h4>
					</div>
					<h5 class="mb-1">Sales Target</h5>
					<p class="mb-0">{{ $salesAchievedText }} Achieved</p>
				</div>

			</div>
		</div>
	</div>
</div>

						</div>
					</div>
					
				</div>	
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
		
        		<!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="https://dexignlab.com/" target="_blank">DexignLab</a> <span class="current-year">2023</span></p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
       
	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->
	<!-- Modal 
	<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Job Title</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal">
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Company Name</label>
							<input type="text" class="form-control solid" placeholder="Name" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Position</label>
							<input type="text" class="form-control solid" placeholder="Name" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Job Category</label>
							<select class="default-select wide form-control solid">
								<option selected="">Choose...</option>
								<option>QA Analyst</option>
								<option>IT Manager</option>
								<option>Systems Analyst</option>
							</select>
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Job Type</label>
							<select class="default-select wide form-control solid">
								<option selected="">Choose...</option>
								<option>Part-Time</option>
								<option>Full-Time</option>
								<option>Freelancer</option>
							</select>
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">No. of Vancancy</label>
							<input type="text" class="form-control solid" placeholder="Name" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Select Experience</label>
							<select class="default-select wide form-control solid">
								<option selected="">1 yr</option>
								<option>2 Yr</option>
								<option>3 Yr</option>
								<option>4 Yr</option>
							</select>
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Posted Date</label>
							<div class="input-hasicon">
								<input name="datepicker" class="form-control solid bt-datepicker">
								<div class="icon"><i class="far fa-calendar"></i></div>
							</div>
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Last Date To Apply</label>
							<div class="input-hasicon">
								<input name="datepicker" class="form-control solid bt-datepicker">
								<div class="icon"><i class="far fa-calendar"></i></div>
							</div>
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Close Date</label>
							<div class="input-hasicon">
								<input name="datepicker" class="form-control solid bt-datepicker">
								<div class="icon"><i class="far fa-calendar"></i></div>
							</div>
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Select Gender:</label>
							<select class="default-select wide form-control solid">
								<option selected="">Choose...</option>
								<option>Male</option>
								<option>Female</option>
							</select>
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Salary Form</label>
							<input type="text" class="form-control solid" placeholder="$" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Salary To</label>
							<input type="text" class="form-control solid" placeholder="$" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Enter City:</label>
							<input type="text" class="form-control solid" placeholder="City" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Enter State:</label>
							<input type="text" class="form-control solid" placeholder="State" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Enter Counter:</label>
							<input type="text" class="form-control solid" placeholder="State" aria-label="name">
						</div>
						<div class="col-xl-6  col-md-6 mb-4">
							<label class="form-label required">Enter Education Level:</label>
							<input type="text" class="form-control solid" placeholder="Education Level" aria-label="name">
						</div>
						<div class="col-xl-12 mb-4">
								<label class="form-label required">Description:</label>
								<textarea class="form-control solid" rows="5" aria-label="With textarea"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>-->

	
<!--**********************************
	Scripts
***********************************-->
<!-- Required vendors -->
<!-- Required vendors -->
<script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Apex Chart -->
<script src="{{ asset('sale_assets/vendor/apexchart/apexchart.js') }}"></script>
<script src="{{ asset('sale_assets/vendor/chartjs/chart.bundle.min.js') }}"></script>

<!-- Chart piety plugin files -->
<script src="{{ asset('sale_assets/vendor/peity/jquery.peity.min.js') }}"></script>

<!-- Dashboard 1 -->
<script src="{{ asset('sale_assets/js/dashboard/dashboard-1.js') }}"></script>

<script src="{{ asset('sale_assets/vendor/owl-carousel/owl.carousel.js') }}"></script>

	<!-- localizationTool -->
	<script src="{{ asset('sale_assets/js/jquery.localizationTool.js') }}"></script>

<script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
<script src="{{ asset('sale_assets/js/dlabnav-init.js') }}"></script>

<!-- Chart initialization using controller data -->
<script>
	(function(){
		try {
			// ===== DATA FROM CONTROLLER =====
			const daily = {
				labels: {!! json_encode($dailyLabels ?? []) !!},
				sales: {!! json_encode($dailySales ?? []) !!},
				orders: {!! json_encode($dailyOrders ?? []) !!},
				returned: {!! json_encode($dailyReturned ?? []) !!}
			};
			
			const weekly = {
				labels: {!! json_encode($weeklyLabels ?? []) !!},
				sales: {!! json_encode($weeklySales ?? []) !!},
				orders: {!! json_encode($weeklyOrders ?? []) !!},
				returned: {!! json_encode($weeklyReturned ?? []) !!}
			};
			
			const monthly = {
				labels: {!! json_encode($months ?? []) !!},
				sales: {!! json_encode($salesData ?? []) !!},
				orders: {!! json_encode($ordersPlacedData ?? []) !!},
				returned: {!! json_encode($ordersReturnedData ?? []) !!}
			};

			const statusData = {!! json_encode($statusData ?? [0,0,0]) !!};
			
			// Order Activity Stats
			const orderActivityStats = {
				daily: { delivered: {{ $dailyDelivered }}, value: {{ (int)$dailySalesValue }} },
				weekly: { delivered: {{ $weeklyDelivered }}, value: {{ (int)$weeklySalesValue }} },
				monthly: { delivered: {{ $monthlyDelivered }}, value: {{ (int)$monthlySalesValue }} }
			};

			// ===== SALES PERFORMANCE CHART =====
			let vacancyChart = null;
			function initVacancyChart(data) {
				const options = {
					series: [
						{ name: 'Total Sales', data: data.sales },
						{ name: 'Orders Placed', data: data.orders },
						{ name: 'Orders Returned', data: data.returned }
					],
					chart: { height: 300, type: 'area', toolbar: { show: false } },
					colors: ["#35c556", "#3f4cfe", "#f34040"],
					dataLabels: { enabled: false },
					stroke: { curve: 'smooth', width: 5 },
					legend: { show: false },
					grid: { show: true, strokeDashArray: 6, borderColor: 'var(--border)' },
					yaxis: {
						labels: {
							style: { colors: 'var(--text)', fontSize: '13px', fontFamily: 'Poppins', fontWeight: 400 }
						}
					},
					xaxis: {
						categories: data.labels,
						labels: { style: { colors: 'var(--text)', fontSize: '13px', fontFamily: 'Poppins', fontWeight: 400 } },
						axisTicks: { show: false },
						axisBorder: { show: false }
					},
					fill: {
						type: 'gradient',
						gradient: {
							colorStops: [
								[
									{ offset: 0, color: '#35c556', opacity: 0.2 },
									{ offset: 50, color: '#35c556', opacity: 0 },
									{ offset: 100, color: '#35c556', opacity: 0 }
								],
								[
									{ offset: 0, color: '#3f4cfe', opacity: 0.2 },
									{ offset: 50, color: '#3f4cfe', opacity: 0 },
									{ offset: 100, color: '#3f4cfe', opacity: 0 }
								],
								[
									{ offset: 0, color: '#f34040', opacity: 0.2 },
									{ offset: 50, color: '#f34040', opacity: 0 },
									{ offset: 100, color: '#f34040', opacity: 0 }
								]
							]
						}
					}
				};
				
				if(vacancyChart) vacancyChart.destroy();
				if(document.getElementById('vacancyChart') && window.ApexCharts){
					vacancyChart = new ApexCharts(document.querySelector("#vacancyChart"), options);
					vacancyChart.render();
				}
			}

			// Initial load with monthly data
			initVacancyChart(monthly);

			// Tab switching for Sales Performance
			document.querySelectorAll('.vacany-tabs .nav-link').forEach(tab => {
				tab.addEventListener('click', function(){
					const period = this.getAttribute('data-series').toLowerCase();
					if(period === 'daily') initVacancyChart(daily);
					else if(period === 'weekly') initVacancyChart(weekly);
					else if(period === 'monthly') initVacancyChart(monthly);
				});
			});

			// ===== ORDER ACTIVITY CHART =====
			let orderActivityChart = null;
			function toOrderActivitySeries(periodData) {
				const labels = periodData.labels || [];
				const orders = periodData.orders || [];
				const returned = periodData.returned || [];
				const sales = periodData.sales || [];
				const delivered = orders.map((o, i) => Math.max((o || 0) - (returned[i] || 0), 0));
				return { labels, delivered, returned, sales };
			}

			function initOrderActivityChart(periodData) {
				const parsed = toOrderActivitySeries(periodData);
				const options = {
					series: [
						{ name: 'Delivered', data: parsed.delivered },
						{ name: 'Returned', data: parsed.returned },
						{ name: 'Sales Value', data: parsed.sales }
					],
					chart: { height: 260, type: 'line', toolbar: { show: false } },
					colors: ['#f73a0b', '#f34040', '#6e6e6e'],
					stroke: { curve: 'smooth', width: [3, 3, 2] },
					dataLabels: { enabled: false },
					grid: { borderColor: 'var(--border)', strokeDashArray: 5 },
					xaxis: {
						categories: parsed.labels,
						labels: { style: { colors: 'var(--text)', fontSize: '12px' } }
					},
					yaxis: {
						labels: { style: { colors: 'var(--text)', fontSize: '12px' } }
					},
					legend: { show: false }
				};

				if (orderActivityChart) {
					orderActivityChart.destroy();
				}
				const chartEl = document.querySelector('#activity1');
				if (chartEl && window.ApexCharts) {
					chartEl.innerHTML = '';
					orderActivityChart = new ApexCharts(chartEl, options);
					orderActivityChart.render();
				}
			}

			// Initial load for Order Activity
			initOrderActivityChart(monthly);

			// ===== ORDER ACTIVITY STATS UPDATE =====
			document.querySelectorAll('.chart-tab .nav-link').forEach(tab => {
				tab.addEventListener('click', function(){
					const period = this.getAttribute('data-series').toLowerCase();
					const stats = orderActivityStats[period];
					const periodData = period === 'daily' ? daily : (period === 'weekly' ? weekly : monthly);
					initOrderActivityChart(periodData);
					if(stats) {
						document.getElementById('ordersDeliveredCount').textContent = stats.delivered;
						document.getElementById('salesValueAmount').textContent = '\u20B9' + stats.value.toLocaleString('en-IN');
					}
				});
			});

			// Sales line chart (if canvas present)
			const salesCanvas = document.getElementById('salesChart');
			if(salesCanvas && window.Chart){
				new Chart(salesCanvas.getContext('2d'), {
					type: 'line',
					data: {
						labels: monthly.labels,
						datasets: [{
							label: 'Monthly Sales',
							data: monthly.sales,
							borderColor: '#ff6a00',
							backgroundColor: 'rgba(255,106,0,0.08)',
							tension: 0.3,
							fill: true
						}]
					},
					options: { responsive: true, maintainAspectRatio: false }
				});
			}

			// Order status pie chart
			const orderCanvas = document.getElementById('orderChart');
			if(orderCanvas && window.Chart){
				new Chart(orderCanvas.getContext('2d'), {
					type: 'doughnut',
					data: {
						labels: ['Delivered','Pending','Cancelled'],
						datasets: [{ data: statusData, backgroundColor: ['#28a745','#ffc107','#dc3545'] }]
					},
					options: { responsive: true, maintainAspectRatio: false }
				});
			}
		} catch(e){ console.error('Chart init error', e); }
	})();
</script>
<script>
	function JobickCarousel(){
		/*  testimonial one function by = owl.carousel.js */
		jQuery('.front-view-slider').owlCarousel({
			loop:false,
			margin:30,
			nav:false,
			autoplaySpeed: 3000,
			navSpeed: 3000,
			autoWidth:true,
			paginationSpeed: 3000,
			slideSpeed: 3000,
			smartSpeed: 3000,
			autoplay: false,
			animateOut: 'fadeOut',
			dots:false,
			navText: ['', ''],
			responsive:{
				0:{
					items:1,
					
					margin:10
				},
				
				480:{
					items:1
				},			
				
				767:{
					items:3
				},
				1750:{
					items:3
				}
			}
		})
	}
	jQuery(window).on('load',function(){
		setTimeout(function(){
			JobickCarousel();
		}, 1000);
	});
</script>
</body>
</html>





