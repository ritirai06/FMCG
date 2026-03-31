
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- PAGE TITLE HERE -->
	<title>FMCG Delivery Panel Dashboard</title>

    <!-- Meta -->
	<meta charset="utf-8">
	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Favicon icon -->
	<link rel="shortcut icon" type="image/png" href="{{ asset('deliver_assets/') }}images/favicon.png">

	<!-- All StyleSheet -->
	<link href="{{ asset('deliver_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
	<link href="{{ asset('deliver_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
	<link href="{{ asset('deliver_assets/vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">

	<!-- Localization Tool (if present) -->
	<link href="{{ asset('deliver_assets/css/jquery.localizationTool.css') }}" rel="stylesheet">

	<!-- Main Style Css -->
	<link class="main-css" href="{{ asset('deliver_assets/css/style.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .dlab-demo-panel,
        .dz-demo-panel,
        .theme-demo,
        .style-switcher,
        .buy-now,
        .buy-btn,
        .support-btn,
        .support-pannel,
        .buybtn,
        #DZ_THEME_PANEL,
        #DZ_W_ThemePanel,
        #DZ_W_Theme,
        a[href*="support.w3itexperts"],
        a[href*="envato.market"] {
            display: none !important;
        }
    </style>
	
</head>
<body>
@php
    $stats = $stats ?? [];
    $todayAssigned = (int) data_get($stats, 'today_assigned', 0);
    $pendingDeliveries = (int) data_get($stats, 'pending', 0);
    $todayDelivered = (int) data_get($stats, 'today_delivered', 0);
    $failedReturned = (int) data_get($stats, 'failed_or_returned', 0);
    $todayEarnings = (float) data_get($stats, 'today_revenue', 0);
    $brandName = $companyName ?? 'FMCG';
    $companyLogo = asset('deliver_assets/images/logo-full.png');
    if (!empty($companySettings?->profile_image)) {
        $logoPath = ltrim((string) $companySettings->profile_image, '/');
        if (str_starts_with($logoPath, 'http')) {
            $companyLogo = $logoPath;
        } elseif (file_exists(public_path('uploads/admin/' . $logoPath))) {
            $companyLogo = asset('uploads/admin/' . $logoPath);
        } elseif (file_exists(public_path('storage/' . $logoPath))) {
            $companyLogo = asset('storage/' . $logoPath);
        }
    }
    $deliveryUserName = $user?->name ?? data_get($deliveryProfile, 'name', 'Delivery Partner');
    $deliveryUserRole = $user?->role ? ucfirst((string) $user->role) : 'Delivery Partner';
    $deliveryLocation = data_get($deliveryProfile, 'location', 'Location N/A');
    $deliveryProfileImage = $companyLogo;
    if (!empty($deliveryProfile?->avatar_path)) {
        $avatarPath = ltrim((string) $deliveryProfile->avatar_path, '/');
        if (str_starts_with($avatarPath, 'http')) {
            $deliveryProfileImage = $avatarPath;
        } elseif (file_exists(public_path('storage/' . $avatarPath))) {
            $deliveryProfileImage = asset('storage/' . $avatarPath);
        } elseif (file_exists(public_path('uploads/admin/' . $avatarPath))) {
            $deliveryProfileImage = asset('uploads/admin/' . $avatarPath);
        }
    }
@endphp
@php
    $recentNotifications = collect($recentOrders ?? collect())->take(8);
    $settingsSummary = collect($statusBreakdown ?? [])
        ->filter(fn ($count) => (int) $count > 0)
        ->take(8);
@endphp


    <div id="main-wrapper">

        		<!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="/delivery-panel/dashboard" class="brand-logo">
                <img class="logo-abbr" src="{{ $companyLogo }}" alt="{{ $brandName }}" style="width:42px;height:42px;object-fit:cover;border-radius:10px;">
				<span class="brand-title" style="font-size: 14px; font-weight: 600; color: #464646; margin-left: 8px;">{{ $brandName }} Delivery Panel</span>

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
					FMCG Delivery Panel Dashboard
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
								<!-- Messages -->
								

								<!-- Notifications -->
								<li class="nav-item dropdown notification_dropdown">
					<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24">
										  <g data-name="Layer 2" transform="translate(-2 -2)">
											<path id="Path_20" data-name="Path 20" d="M22.571,15.8V13.066a8.5,8.5,0,0,0-7.714-8.455V2.857a.857.857,0,0,0-1.714,0V4.611a8.5,8.5,0,0,0-7.714,8.455V15.8A4.293,4.293,0,0,0,2,20a2.574,2.574,0,0,0,2.571,2.571H9.8a4.286,4.286,0,0,0,8.4,0h5.23A2.574,2.574,0,0,0,26,20,4.293,4.293,0,0,0,22.571,15.8ZM7.143,13.066a6.789,6.789,0,0,1,6.78-6.78h.154a6.789,6.789,0,0,1,6.78,6.78v2.649H7.143ZM14,24.286a2.567,2.567,0,0,1-2.413-1.714h4.827A2.567,2.567,0,0,1,14,24.286Zm9.429-3.429H4.571A.858.858,0,0,1,3.714,20a2.574,2.574,0,0,1,2.571-2.571H21.714A2.574,2.574,0,0,1,24.286,20a.858.858,0,0,1-.857.857Z"></path>
										  </g>
										</svg>
						<span class="badge light text-white bg-primary rounded-circle">{{ $recentNotifications->count() }}</span>
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:380px;">
											<ul class="timeline">
                                                @forelse($recentNotifications as $notificationOrder)
                                                    @php
                                                        $nStatus = (string) ($notificationOrder->status ?? 'Order');
                                                        $nBadge = match ($nStatus) {
                                                            'Delivered' => 'success',
                                                            'Failed', 'Returned' => 'danger',
                                                            'Out for Delivery' => 'warning',
                                                            'Picked' => 'info',
                                                            default => 'primary',
                                                        };
                                                        $nAmount = (float) data_get($notificationOrder, 'total_amount', data_get($notificationOrder, 'amount', 0));
                                                    @endphp
                                                    <li>
                                                        <div class="timeline-panel">
                                                            <div class="media me-2">
                                                                <span class="badge bg-{{ $nBadge }} text-white">{{ $nStatus }}</span>
                                                            </div>
                                                            <div class="media-body">
                                                                <h6 class="mb-1">Order {{ $notificationOrder->order_number ?? ('#' . $notificationOrder->id) }} - {{ data_get($notificationOrder, 'store.store_name', 'Store N/A') }} (Rs {{ number_format($nAmount, 2) }})</h6>
                                                                <small class="d-block">{{ optional($notificationOrder->updated_at ?? $notificationOrder->created_at)->diffForHumans() ?? 'Just now' }}</small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li>
                                                        <div class="timeline-panel">
                                                            <div class="media me-2">
                                                                <span class="badge bg-secondary text-white">Info</span>
                                                            </div>
                                                            <div class="media-body">
                                                                <h6 class="mb-1">No notifications yet</h6>
                                                                <small class="d-block">New delivery updates will appear here.</small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforelse
                                            </ul>
										</div>
						<a class="all-notification" href="{{ route('delivery.panel.my.orders') }}">See all notifications</a>
					</div>
					</li>

								<!-- Activity / Timeline -->
								<li class="nav-item dropdown notification_dropdown">
					<a class="nav-link " href="javascript:void(0);" data-bs-toggle="dropdown">
										 <svg xmlns="http://www.w3.org/2000/svg" width="23.262" height="24" viewbox="0 0 23.262 24">
										  <g id="icon" transform="translate(-1565 90)">
											<path id="setting_1_" data-name="setting (1)" d="M30.45,13.908l-1-.822a1.406,1.406,0,0,1,0-2.171l1-.822a1.869,1.869,0,0,0,.432-2.385L28.911,4.293a1.869,1.869,0,0,0-2.282-.818l-1.211.454a1.406,1.406,0,0,1-1.88-1.086l-.213-1.276A1.869,1.869,0,0,0,21.475,0H17.533a1.869,1.869,0,0,0-1.849,1.567L15.47,2.842a1.406,1.406,0,0,1-1.88,1.086l-1.211-.454a1.869,1.869,0,0,0-2.282.818L8.126,7.707a1.869,1.869,0,0,0,.432,2.385l1,.822a1.406,1.406,0,0,1,0,2.171l-1,.822a1.869,1.869,0,0,0-.432,2.385L10.1,19.707a1.869,1.869,0,0,0,2.282.818l1.211-.454a1.406,1.406,0,0,1,1.88,1.086l.213,1.276A1.869,1.869,0,0,0,17.533,24h3.943a1.869,1.869,0,0,0,1.849-1.567l.213-1.276a1.406,1.406,0,0,1,1.88-1.086l1.211.454a1.869,1.869,0,0,0,2.282-.818l1.972-3.415a1.869,1.869,0,0,0-.432-2.385ZM27.287,18.77l-1.211-.454a3.281,3.281,0,0,0-4.388,2.533l-.213,1.276H17.533l-.213-1.276a3.281,3.281,0,0,0-4.388-2.533l-1.211.454L9.75,15.355l1-.822a3.281,3.281,0,0,0,0-5.067l-1-.822L11.721,5.23l1.211.454A3.281,3.281,0,0,0,17.32,3.151l.213-1.276h3.943l.213,1.276a3.281,3.281,0,0,0,4.388,2.533l1.211-.454,1.972,3.414h0l-1,.822a3.281,3.281,0,0,0,0,5.067l1,.822ZM19.5,7.375A4.625,4.625,0,1,0,24.129,12,4.63,4.63,0,0,0,19.5,7.375Zm0,7.375A2.75,2.75,0,1,1,22.254,12,2.753,2.753,0,0,1,19.5,14.75Z" transform="translate(1557.127 -90)"></path>
										  </g>
										</svg>

										<span class="badge light text-white bg-primary rounded-circle">{{ $settingsSummary->count() }}</span>
					</a>
									<div class="dropdown-menu dropdown-menu-end">
										<div id="DZ_W_TimeLine02" class="widget-timeline dlab-scroll style-1 p-3 height370">
						<ul class="timeline">
                        @forelse($settingsSummary as $statusName => $statusCount)
                            @php
                                $sClass = match ((string) $statusName) {
                                    'Delivered' => 'success',
                                    'Failed', 'Returned' => 'danger',
                                    'Out for Delivery' => 'warning',
                                    'Picked' => 'info',
                                    default => 'primary',
                                };
                            @endphp
                            <li>
                                <div class="timeline-badge {{ $sClass }}"></div>
                                <a class="timeline-panel text-muted" href="{{ route('delivery.panel.my.orders') }}">
                                    <span>{{ now()->diffForHumans() }}</span>
                                    <h6 class="mb-0">{{ $statusName }} Orders: {{ (int) $statusCount }}</h6>
                                </a>
                            </li>
                        @empty
                            <li>
                                <div class="timeline-badge dark"></div>
                                <a class="timeline-panel text-muted" href="{{ route('delivery.panel.my.orders') }}">
                                    <span>Just now</span>
                                    <h6 class="mb-0">No delivery settings insights available.</h6>
                                </a>
                            </li>
                        @endforelse
						</ul>
					</div>
									</div>
								</li>

								<!-- User Profile -->
								<li class="nav-item dropdown header-profile">
					<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
						<img src="{{ $deliveryProfileImage }}" width="20" alt="{{ $deliveryUserName }}">
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<a href="{{ route('delivery.panel.profile') }}" class="dropdown-item ai-icon">
						<svg id="icon-user2" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
						<span class="ms-2">My Profile</span>
						</a>
						<a href="{{ route('delivery.panel.attendance') }}" class="dropdown-item ai-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
						<span class="ms-2">Attendance</span>
						</a>
						<a href="javascript:void(0);" class="dropdown-item ai-icon" onclick="if(confirm('Are you sure you want to logout?')){ event.preventDefault(); document.getElementById('deliveryLogoutForm').submit(); }">
						<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
						<span class="ms-2">Logout</span>
						</a>
					</div>
					</li>
				</ul>
                <form id="deliveryLogoutForm" action="{{ route('delivery.panel.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
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
							<img src="{{ $deliveryProfileImage }}" alt="{{ $deliveryUserName }}">
							<div class="d-flex align-items-center sidebar-info">
								<div>
									<span class="font-w400 d-block">{{ $deliveryUserName }}</span>
									<small class="text-end font-w400">{{ $deliveryUserRole }}</small>
								</div>	
								<i class="fas fa-chevron-down"></i>
							</div>
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<a href="/delivery-panel/profile" class="dropdown-item ai-icon ">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
							<span class="ms-2">Profile </span>
						</a>
						<a href="/delivery-panel/dashboard" class="dropdown-item ai-icon">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
							<span class="ms-2">Dashboard </span>
						</a>
						<a href="javascript:void(0);" class="dropdown-item ai-icon" onclick="if(confirm('Are you sure you want to logout?')){ event.preventDefault(); document.getElementById('deliveryLogoutForm').submit(); }">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
							<span class="ms-2">Logout </span>
						</a>
					</div>
				</div>
				<ul class="metismenu" id="menu">
                    <li><a class="has-arrow" href="{{ route('delivery.panel.dashboard') }}"><i class="flaticon-025-dashboard"></i><span class="nav-text">Dashboard</span></a></li>
                    <li><a class="has-arrow" href="{{ route('delivery.panel.my.orders') }}"><i class="flaticon-381-user-7"></i><span class="nav-text">Orders</span></a></li>
                    <li><a class="has-arrow" href="{{ route('delivery.panel.order.details') }}"><i class="flaticon-381-notepad"></i><span class="nav-text">Order Details</span></a></li>
                    <li><a class="has-arrow" href="{{ route('delivery.panel.attendance') }}"><i class="flaticon-381-user-4"></i><span class="nav-text">Attendance</span></a></li>
                    <li><a class="has-arrow" href="{{ route('delivery.panel.profile') }}"><i class="flaticon-381-internet"></i><span class="nav-text">Profile</span></a></li>
                </ul>
				<div class="plus-box">
					<p class="fs-14 font-w600 mb-2">Let FMCG simplify<br>your delivery workflow</p>
					<p class="plus-box-p">Manage deliveries, orders, and reports in one place</p>
				</div>
				<div class="copyright">
					<p><strong>{{ $brandName }}</strong> - Delivery Panel &copy; <span class="current-year">2026</span></p>
					<p class="fs-12">Manage deliveries, orders, and reports in one place</p>
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
								<h2 class="mb-0 lh-1">{{ $todayAssigned }}</h2>
							</div>
							<span class="d-block mb-2">Today Assigned Orders</span>
						</div>
						<div id="NewCustomers"></div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="job-icon pb-4 pt-4 pt-sm-0 d-flex justify-content-between">
						<div>
							<div class="d-flex align-items-center mb-1">
								<h2 class="mb-0 lh-1">&#8377;{{ number_format($todayEarnings, 2) }}</h2>
							</div>
							<span class="d-block mb-2">Today Earnings (if commission based)</span>
						</div>
						<div id="NewCustomers1"></div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="job-icon pt-4 pb-sm-0 pb-4 d-flex justify-content-between">
						<div>
							<div class="d-flex align-items-center mb-1">
								<h2 class="mb-0 lh-1">{{ $todayDelivered }}</h2>
							</div>
							<span class="d-block mb-2">Delivered Today</span>
						</div>
						<div id="NewCustomers2"></div>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="job-icon pt-4 d-flex justify-content-between">
						<div>
							<div class="d-flex align-items-center mb-1">
								<h2 class="mb-0 lh-1 text-danger">{{ $failedReturned }}</h2>
							</div>
							<span class="d-block mb-2">Failed / Returned</span>
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
			<h4 class="card-title mb-0">Delivery Performance</h4>
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

				<!-- Orders Delivered -->
				<div class="d-flex align-items-center">
					<svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" rx="6.5" fill="#35c556"></rect>
					</svg>
					<span class="text-dark fs-13 font-w500">Orders Delivered</span>
				</div>

				<!-- Out for Delivery -->
				<div class="application d-flex align-items-center">
					<svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" rx="6.5" fill="#3f4cfe"></rect>
					</svg>
					<span class="text-dark fs-13 font-w500">Out for Delivery</span>
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
					<h6 class="mb-0 me-1" id="ordersDeliveredCount">4</h6>
					<span class="text-success fs-13 font-w500">+4%</span>
				</div>

				<!-- SALES VALUE -->
				<div class="d-flex align-items-center">
					<svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13">
						<rect width="13" height="13" fill="#6e6e6e"></rect>
					</svg>
					<label class="form-label mb-0 me-4">Sales Value</label>
					<h6 class="mb-0 me-1" id="salesValueAmount">&#8377;{{ number_format($todayEarnings, 2) }}</h6>
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
											<div class="col-xl-6 col-sm-6">
												<div class="d-flex align-items-center list-item-bx">
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
														<span>Andheri ? Mumbai</span>
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
							<span>Ghatkopar ? Mumbai</span>
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
							<span>Kanjurmarg ? Mumbai</span>
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
							<span>Bhandup ? Mumbai</span>

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
			<img src="{{ $deliveryProfileImage }}" alt="">
			<div class="ms-4">
				<h3 class="mb-0">{{ $deliveryUserName }}</h3>
				<span class="text-primary d-block mb-xl-3 mb-1">{{ $deliveryUserRole }}</span>
				<span><i class="fas fa-map-marker-alt me-1"></i>{{ $deliveryLocation }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-4 col-xxl-5 col-sm-5 sm-mt-auto mt-3 text-sm-end">
		<a href="{{ route('delivery.panel.my.orders') }}" class="btn btn-primary">View Orders</a>
	</div>
</div>

<div class="row mt-4 align-items-center">
	<h4 class="fs-20 mb-3 mt-1">Delivery Dashboard Summary</h4>
	<div class="col-12">
		<ul class="list-unstyled mb-3">
			<li class="d-flex justify-content-between mb-3"><span>Today Assigned Orders</span><strong>{{ $todayAssigned }}</strong></li>
			<li class="d-flex justify-content-between mb-3"><span>Pending Deliveries</span><strong>{{ $pendingDeliveries }}</strong></li>
			<li class="d-flex justify-content-between mb-3"><span>Delivered Today</span><strong>{{ $todayDelivered }}</strong></li>
			<li class="d-flex justify-content-between mb-3"><span>Failed / Returned Count</span><strong>{{ $failedReturned }}</strong></li>
			<li class="d-flex justify-content-between mb-0"><span>Today Earnings (if commission based)</span><strong>&#8377;{{ number_format($todayEarnings, 2) }}</strong></li>
		</ul>
		<a href="{{ route('delivery.panel.my.orders') }}" class="btn btn-primary w-100">View Orders</a>
	</div>
</div>
		</div>
	</div>
</div>
</div>

							<div class="col-xl-12">
								<div class="card">
									<div class="card-header border-0 pb-2">
										<h4 class="card-title mb-0">Recent Delivery Activity</h4>
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
																				<div class="d-flex recent-activity">
											<span class="me-3 activity">
												<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewbox="0 0 17 17">
												  <circle cx="8.5" cy="8.5" r="8.5" fill=" #28a745 "></circle>
												</svg>
											</span>
											<div class="d-flex align-items-center list-item-bx">
												<div class="icon-img-bx">
													<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
														<g transform="translate(-457 -443)">
															<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
															<g transform="translate(457 443)">
															<rect data-name="placeholder" width="71" height="71" rx="12" fill=" #22bc32 "></rect>
															<circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
															<circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
															</g>
														</g>
													</svg>
												</div>
												<div class="ms-3">
													<h6 class="mb-1">Order #5 ? Daily Needs Store ? Out for Delivery</h6>
													<p class="mb-0">?36,871 ? 1 day ago</p>
												</div>
											</div>
										</div>
																				<div class="d-flex recent-activity">
											<span class="me-3 activity">
												<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewbox="0 0 17 17">
												  <circle cx="8.5" cy="8.5" r="8.5" fill=" #28a745 "></circle>
												</svg>
											</span>
											<div class="d-flex align-items-center list-item-bx">
												<div class="icon-img-bx">
													<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
														<g transform="translate(-457 -443)">
															<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
															<g transform="translate(457 443)">
															<rect data-name="placeholder" width="71" height="71" rx="12" fill=" #22bc32 "></rect>
															<circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
															<circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
															</g>
														</g>
													</svg>
												</div>
												<div class="ms-3">
													<h6 class="mb-1">Order #6 ? City Mart ? Delivered</h6>
													<p class="mb-0">?48,974 ? 1 day ago</p>
												</div>
											</div>
										</div>
																				<div class="d-flex recent-activity">
											<span class="me-3 activity">
												<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewbox="0 0 17 17">
												  <circle cx="8.5" cy="8.5" r="8.5" fill=" #28a745 "></circle>
												</svg>
											</span>
											<div class="d-flex align-items-center list-item-bx">
												<div class="icon-img-bx">
													<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
														<g transform="translate(-457 -443)">
															<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
															<g transform="translate(457 443)">
															<rect data-name="placeholder" width="71" height="71" rx="12" fill=" #22bc32 "></rect>
															<circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
															<circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
															</g>
														</g>
													</svg>
												</div>
												<div class="ms-3">
													<h6 class="mb-1">Order #7 ? Daily Needs Store ? Out for Delivery</h6>
													<p class="mb-0">?34,554 ? 1 day ago</p>
												</div>
											</div>
										</div>
																				<div class="d-flex recent-activity">
											<span class="me-3 activity">
												<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewbox="0 0 17 17">
												  <circle cx="8.5" cy="8.5" r="8.5" fill=" #ffc107 "></circle>
												</svg>
											</span>
											<div class="d-flex align-items-center list-item-bx">
												<div class="icon-img-bx">
													<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
														<g transform="translate(-457 -443)">
															<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
															<g transform="translate(457 443)">
															<rect data-name="placeholder" width="71" height="71" rx="12" fill=" #eeac27 "></rect>
															<circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
															<circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
															</g>
														</g>
													</svg>
												</div>
												<div class="ms-3">
													<h6 class="mb-1">Order #8 ? Quality Retail ? Pending</h6>
													<p class="mb-0">?40,502 ? 1 day ago</p>
												</div>
											</div>
										</div>
																				<div class="d-flex recent-activity">
											<span class="me-3 activity">
												<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewbox="0 0 17 17">
												  <circle cx="8.5" cy="8.5" r="8.5" fill=" #28a745 "></circle>
												</svg>
											</span>
											<div class="d-flex align-items-center list-item-bx">
												<div class="icon-img-bx">
													<svg xmlns="http://www.w3.org/2000/svg" width="71" height="71" viewbox="0 0 71 71">
														<g transform="translate(-457 -443)">
															<rect width="71" height="71" rx="12" transform="translate(457 443)" fill="#c5c5c5"></rect>
															<g transform="translate(457 443)">
															<rect data-name="placeholder" width="71" height="71" rx="12" fill=" #22bc32 "></rect>
															<circle data-name="Ellipse 12" cx="18" cy="18" r="18" transform="translate(15 20)" fill="#fff"></circle>
															<circle data-name="Ellipse 11" cx="11" cy="11" r="11" transform="translate(36 15)" fill="#ffe70c" style="mix-blend-mode: multiply;isolation: isolate"></circle>
															</g>
														</g>
													</svg>
												</div>
												<div class="ms-3">
													<h6 class="mb-1">Order #9 ? Fresh Basket ? Delivered</h6>
													<p class="mb-0">?42,386 ? 1 day ago</p>
												</div>
											</div>
										</div>
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
												<tr>
							<td>
								<h6 class="mb-0">Sharma General Store</h6>
							</td>
							<td>
								<span class="badge badge-primary">SGS001</span>
							</td>
							<td>Rajesh Sharma</td>
							<td>
								<small class="text-muted">Andheri, Mumbai</small>
							</td>
							<td>
																	<span class="badge badge-warning">2 Pending</span>
															</td>
							<td>
								<span class="badge badge-success">{{ $todayDelivered }}</span>
							</td>
							<td>
								<strong>?122,767</strong>
							</td>
						</tr>
												<tr>
							<td>
								<h6 class="mb-0">City Mart</h6>
							</td>
							<td>
								<span class="badge badge-primary">CM002</span>
							</td>
							<td>Priya Nair</td>
							<td>
								<small class="text-muted">Ghatkopar, Mumbai</small>
							</td>
							<td>
																	<span class="badge badge-warning">1 Pending</span>
															</td>
							<td>
								<span class="badge badge-success">{{ $todayDelivered }}</span>
							</td>
							<td>
								<strong>?59,583</strong>
							</td>
						</tr>
												<tr>
							<td>
								<h6 class="mb-0">Fresh Basket</h6>
							</td>
							<td>
								<span class="badge badge-primary">FB003</span>
							</td>
							<td>Amit Kumar</td>
							<td>
								<small class="text-muted">Kanjurmarg, Mumbai</small>
							</td>
							<td>
																	<span class="badge badge-warning">2 Pending</span>
															</td>
							<td>
								<span class="badge badge-success">{{ $todayDelivered }}</span>
							</td>
							<td>
								<strong>?132,634</strong>
							</td>
						</tr>
												<tr>
							<td>
								<h6 class="mb-0">Daily Needs Store</h6>
							</td>
							<td>
								<span class="badge badge-primary">DNS004</span>
							</td>
							<td>Sneha Patel</td>
							<td>
								<small class="text-muted">Bhandup, Mumbai</small>
							</td>
							<td>
																	<span class="badge badge-warning">5 Pending</span>
															</td>
							<td>
								<span class="badge badge-success">{{ $failedReturned }}</span>
							</td>
							<td>
								<strong>?158,623</strong>
							</td>
						</tr>
												<tr>
							<td>
								<h6 class="mb-0">Quality Retail</h6>
							</td>
							<td>
								<span class="badge badge-primary">QR005</span>
							</td>
							<td>Vikram Singh</td>
							<td>
								<small class="text-muted">Powai, Mumbai</small>
							</td>
							<td>
																	<span class="badge badge-warning">2 Pending</span>
															</td>
							<td>
								<span class="badge badge-success">{{ $todayDelivered }}</span>
							</td>
							<td>
								<strong>?182,275</strong>
							</td>
						</tr>
												<tr>
							<td>
								<h6 class="mb-0">ss</h6>
							</td>
							<td>
								<span class="badge badge-primary">STR260222129</span>
							</td>
							<td>jfjdf</td>
							<td>
								<small class="text-muted">ffgfg</small>
							</td>
							<td>
																	<span class="badge badge-success">No Pending</span>
															</td>
							<td>
								<span class="badge badge-success">{{ $failedReturned }}</span>
							</td>
							<td>
								<strong>?0</strong>
							</td>
						</tr>
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
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>6/6</span>
						<h4 class="mb-0 pie-label">100%</h4>
					</div>
					<h5 class="mb-1">Retail Stores</h5>
					<p class="mb-0">6 Covered</p>
				</div>

				<div class="col-xl-3 col-md-3 col-6 mb-4 text-center">
					<div class="d-inline-block ms-auto me-auto mb-md-3 mb-2 db-donut-chart-sale me-4">
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>0/0</span>
						<h4 class="mb-0 pie-label">0%</h4>
					</div>
					<h5 class="mb-1">Wholesale Clients</h5>
					<p class="mb-0">0 Active</p>
				</div>

				<div class="col-xl-3 col-md-3 col-6 mb-4 text-center">
					<div class="d-inline-block ms-auto me-auto mb-md-3 mb-2 db-donut-chart-sale me-4">
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>4/20</span>
						<h4 class="mb-0 pie-label">20%</h4>
					</div>
					<h5 class="mb-1">Orders Delivered</h5>
					<p class="mb-0">4 Completed</p>
				</div>

				<div class="col-xl-3 col-md-3 col-6 mb-4 text-center">
					<div class="d-inline-block ms-auto me-auto mb-md-3 mb-2 db-donut-chart-sale me-4">
						<span class="donut" data-peity='{ "fill": ["var(--primary)", "var(--light)"], "innerRadius": 38, "radius": 10}'>44/100</span>
						<h4 class="mb-0 pie-label">44%</h4>
					</div>
					<h5 class="mb-1">Sales Target</h5>
					<p class="mb-0">?6.6L Achieved</p>
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
                <p>Copyright &copy; {{ $brandName }} - Delivery Panel <span class="current-year">2026</span></p>
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
<script src="{{ asset('deliver_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('deliver_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('deliver_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Apex Chart -->
<script src="{{ asset('deliver_assets/vendor/apexchart/apexchart.js') }}"></script>
<script src="{{ asset('deliver_assets/vendor/chartjs/chart.bundle.min.js') }}"></script>

<!-- Chart piety plugin files -->
<script src="{{ asset('deliver_assets/vendor/peity/jquery.peity.min.js') }}"></script>

<!-- Dashboard 1 -->
<script src="{{ asset('deliver_assets/js/dashboard/dashboard-1.js') }}"></script>

<script src="{{ asset('deliver_assets/vendor/owl-carousel/owl.carousel.js') }}"></script>

	<!-- localizationTool -->
	<script src="{{ asset('deliver_assets/js/jquery.localizationTool.js') }}"></script>

<script src="{{ asset('deliver_assets/js/custom.min.js') }}"></script>
<script src="{{ asset('deliver_assets/js/dlabnav-init.js') }}"></script>

<!-- Chart initialization using controller data -->
<script>
	(function(){
		try {
			// ===== DATA FROM CONTROLLER =====
			const daily = {
				labels: ["Tue","Wed","Thu","Fri","Sat","Sun","Mon"],
				sales: [0,0,0,0,655882,0,0],
				orders: [0,0,0,0,20,0,0],
				returned: [0,0,0,0,4,0,0]
			};
			
			const weekly = {
				labels: ["W4","W3","W2","W1"],
				sales: [0,0,655882,0],
				orders: [0,0,20,0],
				returned: [0,0,4,0]
			};
			
			const monthly = {
				labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
				sales: [0,655882,0,0,0,0,0,0,0,0,0,0],
				orders: [0,20,0,0,0,0,0,0,0,0,0,0],
				returned: [0,4,0,0,0,0,0,0,0,0,0,0]
			};

			const statusData = [4,6,4];
			
			// Order Activity Stats
			const orderActivityStats = {
				daily: { delivered: 0, value: 0 },
				weekly: { delivered: 0, value: 0 },
				monthly: { delivered: 4, value: 655882 }
			};

			// ===== SALES PERFORMANCE CHART =====
			let vacancyChart = null;
			function initVacancyChart(data) {
				const options = {
					series: [
						{ name: 'Orders Delivered', data: data.sales },
						{ name: 'Out for Delivery', data: data.orders },
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

			// ===== ORDER ACTIVITY STATS UPDATE =====
			document.querySelectorAll('.chart-tab .nav-link').forEach(tab => {
				tab.addEventListener('click', function(){
					const period = this.getAttribute('data-series').toLowerCase();
					const stats = orderActivityStats[period];
					if(stats) {
						document.getElementById('ordersDeliveredCount').textContent = stats.delivered;
						document.getElementById('salesValueAmount').textContent = '?' + stats.value.toLocaleString('en-IN');
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
<script>
    (function () {
        function removeSupportBuyNow() {
            var selectors = [
                'a[href*="support.w3itexperts"]',
                'a[href*="envato.market"]',
                '.support-btn',
                '.support-pannel',
                '.buy-now',
                '.buy-btn',
                '.buybtn',
                '#DZ_THEME_PANEL',
                '#DZ_W_ThemePanel',
                '#DZ_W_Theme'
            ];
            selectors.forEach(function (selector) {
                document.querySelectorAll(selector).forEach(function (el) {
                    el.remove();
                });
            });
            document.querySelectorAll('a,button,div,span').forEach(function (el) {
                var txt = (el.textContent || '').trim().toUpperCase();
                if (txt === 'SUPPORT' || txt === 'BUY NOW') {
                    el.remove();
                }
            });
        }
        removeSupportBuyNow();
        setTimeout(removeSupportBuyNow, 300);
        setTimeout(removeSupportBuyNow, 1000);
    })();
</script>
</body>
</html>


