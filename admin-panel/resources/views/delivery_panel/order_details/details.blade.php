<!DOCTYPE html>
<html lang="en">
<head>

   <!-- PAGE TITLE HERE -->
	<title>{{ preg_replace('/sales\s*panel/i', 'Delivery Panel', (string) ($companyName ?? 'Delivery Panel')) ?: ($companyName ?? 'Delivery Panel') }} Order Details</title>

    <!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<!-- Canonical URL -->
	<link rel="canonical" href="new-job.html">

	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Favicon icon -->
	<link rel="shortcut icon" type="image/png" href="{{ asset('deliver_assets/images/favicon.png') }}">
	<link href="{{ asset('deliver_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
	<link href="{{ asset('deliver_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
	
	<!-- Localization Tool -->
	<link href="{{ asset('deliver_assets/css/jquery.localizationTool.css') }}" rel="stylesheet">
	
	<!-- Style Css -->
	<link class="main-css" href="{{ asset('deliver_assets/css/style.css') }}" rel="stylesheet">
	<style>
		a[href*="support.w3itexperts"],
		a[href*="envato.market"],
		.sidebar-right,
		.sidebar-right-trigger,
		.sidebar-close-trigger,
		.dlab-demo-panel,
		.dlab-demo-trigger {
			display: none !important;
			visibility: hidden !important;
		}
		.order-right .card {
			border: 1px solid #eceff3;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
			border-radius: 12px;
			overflow: hidden;
			height: auto !important;
			min-height: 0 !important;
		}
		.order-right {
			max-width: none;
			width: 100%;
		}
		.order-right .card-title {
			font-weight: 600;
			margin-bottom: 0;
		}
		.order-right .card-header {
			padding: 16px 20px 12px;
			background: #fff;
		}
		.order-right table th,
		.order-right table td {
			vertical-align: middle;
		}
		.order-right table thead th {
			font-size: 12px;
			letter-spacing: .2px;
			color: #6b7280;
			font-weight: 600;
			background: #fafbfc;
		}
		.order-right .card-body {
			padding: 18px 20px;
			height: auto !important;
		}
		.order-right .card-body.p-0 {
			padding: 0 !important;
		}
		.order-right .timeline-item:last-child {
			padding-bottom: 0 !important;
			margin-bottom: 0 !important;
			border-bottom: 0 !important;
		}
		.order-right .form-control,
		.order-right .form-select {
			min-height: 44px;
			border-radius: 10px;
		}
		.order-right .btn.w-100 {
			min-height: 44px;
			font-weight: 600;
			border-radius: 10px;
		}
		.order-right .store-card,
		.order-right .store-card .card-body {
			height: auto !important;
			min-height: 0 !important;
		}
		.order-right .store-card .row {
			row-gap: 12px;
		}
		.order-right .store-card .row > [class*="col-"] {
			margin-bottom: 0 !important;
		}
		.order-right .detail-label {
			font-size: 13px;
			color: #8a94a6;
			margin-bottom: 6px;
		}
		.order-right .detail-value {
			font-size: 24px;
			font-weight: 700;
			color: #101828;
			line-height: 1.2;
			word-break: break-word;
		}
		.order-right .detail-value-sm {
			font-size: 18px;
			font-weight: 600;
			color: #101828;
			line-height: 1.2;
			word-break: break-word;
		}
		body.modal-order-view .nav-header,
		body.modal-order-view .header,
		body.modal-order-view .dlabnav,
		body.modal-order-view .footer,
		body.modal-order-view .plus-box,
		body.modal-order-view .copyright,
		body.modal-order-view .order-navigator-card,
		body.modal-order-view .btn-light.me-3 {
			display: none !important;
		}
		body.modal-order-view .content-body {
			margin-left: 0 !important;
			min-height: auto !important;
		}
		body.modal-order-view .container-fluid {
			padding: 16px !important;
		}
		/* Keep button colors stable on hover/focus to avoid white state glitches */
		.btn-primary,
		.btn-primary:hover,
		.btn-primary:focus,
		.btn-primary:active,
		.btn-primary:focus-visible {
			background-color: #f73a0b !important;
			border-color: #f73a0b !important;
			color: #ffffff !important;
		}
		.btn-success:hover,
		.btn-success:focus,
		.btn-success:active,
		.btn-warning:hover,
		.btn-warning:focus,
		.btn-warning:active,
		.btn-danger:hover,
		.btn-danger:focus,
		.btn-danger:active {
			color: #ffffff !important;
		}
		.btn:disabled,
		.btn.disabled {
			opacity: .65 !important;
		}
	</style>
	

</head>
@php $isModalView = request()->boolean('modal'); @endphp
<body class="{{ $isModalView ? 'modal-order-view' : '' }}">

    	<!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
		@php
			$resolveImage = function ($path, $fallback) {
				$path = trim((string) $path);
				if ($path === '') {
					return $fallback;
				}
				if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
					return $path;
				}
				$path = ltrim($path, '/');
				$candidates = [
					$path,
					'storage/' . $path,
					'uploads/' . $path,
					'uploads/company/' . $path,
					'uploads/admin/' . $path,
					'uploads/delivery/' . $path,
				];
				foreach ($candidates as $candidate) {
					$fullPath = public_path($candidate);
					if (file_exists($fullPath)) {
						return asset($candidate);
					}
				}
				return $fallback;
			};
			$companyLogo = $resolveImage(
				$companySettings?->company_logo
					?? $companySettings?->logo
					?? $companySettings?->profile_image
					?? null,
				asset('deliver_assets/images/logo-full.png')
			);
			$deliveryProfileImage = $resolveImage(
				$deliveryProfile?->avatar_path
					?? $deliveryProfile?->profile_image
					?? $user?->avatar
					?? null,
				$companyLogo
			);
			$panelCompanyName = preg_replace('/sales\s*panel/i', 'Delivery Panel', (string) ($companyName ?? 'Delivery Panel')) ?: ($companyName ?? 'Delivery Panel');
			$deliveryUserName = $deliveryProfile?->name ?? $user?->name ?? 'Delivery Executive';
			$deliveryUserRole = ucfirst((string) ($user?->role ?? 'delivery'));
			$orderItems = $order->items ?? collect();
			$computedSubtotal = $orderItems->sum(function ($item) {
				return (float) ($item->subtotal ?? $item->total ?? (($item->quantity ?? 0) * ($item->unit_price ?? $item->price ?? 0)));
			});
			$orderTotal = (float) ($order->total_amount ?? $order->amount ?? $computedSubtotal);
			$discountAmount = max(0, $computedSubtotal - $orderTotal);
			$shippingAmount = max(0, $orderTotal - ($computedSubtotal - $discountAmount));
			$headerEvents = collect($recentOrders ?? collect())->sortByDesc(function ($row) {
				return data_get($row, 'updated_at') ?? data_get($row, 'created_at');
			})->values();
			$notificationCount = $headerEvents->take(9)->count();
			$settingsCount = $headerEvents->take(5)->count();
		@endphp

        		<!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="{{ route('delivery.panel.dashboard') }}" class="brand-logo">
				<img class="logo-abbr" src="{{ $companyLogo }}" alt="{{ $panelCompanyName }}" style="width:42px;height:42px;object-fit:cover;border-radius:10px;">
				<div class="brand-title">
					<h2 class="mb-0">{{ $panelCompanyName }}</h2>
					<span style="font-size:12px;color:#787878;display:block;line-height:1;">Delivery Panel</span>
				</div>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->
        
				<!--**********************************
     
         
            Header start
        ***********************************-->
<div class="header">
			<div class="header-content">
			<nav class="navbar navbar-expand">
				<div class="collapse navbar-collapse justify-content-between">
				<div class="header-left">
								<div class="dashboard_bar">
					{{ $panelCompanyName }} Order Details
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
						<span class="badge light text-white bg-primary rounded-circle">{{ $notificationCount }}</span>
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:380px;">
											<ul class="timeline">
											@forelse($headerEvents->take(5) as $recentOrder)
												@php
													$status = (string) ($recentOrder->status ?? 'Order');
													$badgeClass = match ($status) {
														'Delivered' => 'success',
														'Returned', 'Failed' => 'danger',
														'Out for Delivery' => 'info',
														'Picked' => 'warning',
														'Assigned', 'Pending' => 'primary',
														default => 'secondary',
													};
												@endphp
												<li>
													<div class="timeline-panel">
														<div class="media me-2">
															<span class="badge bg-{{ $badgeClass }} text-white">{{ $status }}</span>
														</div>
														<div class="media-body">
															<h6 class="mb-1">Order {{ $recentOrder->order_number ?? ('#' . $recentOrder->id) }} - {{ data_get($recentOrder, 'store.store_name', 'Store N/A') }}</h6>
															<small class="d-block">{{ optional($recentOrder->created_at)->diffForHumans() ?? 'Just now' }}</small>
														</div>
													</div>
												</li>
											@empty
												<li>
													<div class="timeline-panel">
														<div class="media-body">
															<h6 class="mb-1">No notifications yet</h6>
															<small class="d-block">Orders will appear here once assigned.</small>
														</div>
													</div>
												</li>
											@endforelse
											</ul>
										</div>
						<a class="all-notification" href="javascript:void(0);">See all notifications</a>
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

										<span class="badge light text-white bg-primary rounded-circle">{{ $settingsCount }}</span>
					</a>
									<div class="dropdown-menu dropdown-menu-end">
										<div id="DZ_W_TimeLine02" class="widget-timeline dlab-scroll style-1 p-3 height370">
						<ul class="timeline">
						@forelse($headerEvents->take(5) as $recentOrder)
							@php
								$timelineClass = match ((string) ($recentOrder->status ?? '')) {
									'Delivered' => 'success',
									'Out for Delivery' => 'info',
									'Returned', 'Failed' => 'warning',
									'Assigned', 'Pending' => 'primary',
									default => 'dark',
								};
								$timelineAmount = (float) data_get($recentOrder, 'total_amount', data_get($recentOrder, 'amount', 0));
							@endphp
							<li>
								<div class="timeline-badge {{ $timelineClass }}"></div>
								<a class="timeline-panel text-muted" href="javascript:void(0);">
									<span>{{ optional($recentOrder->created_at)->diffForHumans() ?? 'Just now' }}</span>
									<h6 class="mb-0">{{ $recentOrder->status ?? 'Order' }} - {{ $recentOrder->order_number ?? ('#' . $recentOrder->id) }} (Rs {{ number_format($timelineAmount, 2) }})</h6>
								</a>
							</li>
						@empty
							<li>
								<div class="timeline-badge dark"></div>
								<a class="timeline-panel text-muted" href="javascript:void(0);">
									<span>No recent activity</span>
									<h6 class="mb-0">New order updates will appear here.</h6>
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
						<form action="{{ route('delivery.panel.logout') }}" method="POST" class="m-0">
							@csrf
							<button type="submit" class="dropdown-item ai-icon border-0 bg-transparent w-100 text-start">
								<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
								<span class="ms-2">Logout</span>
							</button>
						</form>
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
						<a href="{{ route('delivery.panel.profile') }}" class="dropdown-item ai-icon ">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
							<span class="ms-2">Profile </span>
						</a>
						<form action="{{ route('delivery.panel.logout') }}" method="POST" class="m-0">
							@csrf
							<button type="submit" class="dropdown-item ai-icon border-0 bg-transparent w-100 text-start">
								<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
								<span class="ms-2">Logout </span>
							</button>
						</form>
					</div>
				</div>
				<ul class="metismenu" id="menu">
					<li>
						<a class="has-arrow" href="{{ route('delivery.panel.dashboard') }}" aria-expanded="false">
							<i class="flaticon-025-dashboard"></i>
							<span class="nav-text">Dashboard</span>
						</a>
					</li>
					<li>
						<a class="has-arrow" href="{{ route('delivery.panel.my.orders') }}" aria-expanded="false">
							<i class="flaticon-381-user-7"></i>
							<span class="nav-text">Orders</span>
						</a>
					</li>
					<li>
						<a class="has-arrow" href="{{ route('delivery.panel.order.details') }}" aria-expanded="false">
							<i class="flaticon-381-notepad"></i>
							<span class="nav-text">Order Details</span>
						</a>
					</li>
					<li>
						<a class="has-arrow" href="{{ route('delivery.panel.profile') }}" aria-expanded="false">
							<i class="flaticon-381-internet"></i>
							<span class="nav-text">Profile</span>
						</a>
					</li>
				</ul>

				<div class="plus-box">
					<p class="fs-14 font-w600 mb-2">Let {{ $panelCompanyName }} simplify<br>your delivery workflow</p>
					<p class="plus-box-p">Manage deliveries, orders, and reports in one place</p>
				</div>
				<div class="copyright">
					<p><strong>{{ $panelCompanyName }}</strong> - Delivery Panel &copy; <span class="current-year">2023</span></p>
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
	<div class="container-fluid">
		<div class="d-flex align-items-center mb-4">
			<div>
				<a href="javascript:history.back();" class="btn btn-sm btn-light me-3"><i class="fas fa-arrow-left"></i></a>
				<h3 class="mb-0 d-inline">Order Details</h3>
			</div>
			<div class="ms-auto">
				<span class="badge bg-primary fs-5 me-2">{{ $order->order_number ?? ('Order #' . ($order->id ?? 'N/A')) }}</span>
				<span class="badge @if($order->status == 'Delivered') bg-success @elseif($order->status == 'Out for Delivery') bg-warning @elseif($order->status == 'Cancelled') bg-danger @else bg-info @endif fs-5">{{ $order->status ?? 'Pending' }}</span>
			</div>
		</div>

		<div class="row">
			<div class="col-12 order-right">
				<div class="card mb-4 order-navigator-card">
					<div class="card-header border-0">
						<h5 class="card-title">Picked / Assigned Orders</h5>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-striped mb-0">
								<thead>
									<tr class="border-top">
										<th class="px-4 py-3">Order ID</th>
										<th class="px-4 py-3">Store</th>
										<th class="px-4 py-3 text-center">Status</th>
										<th class="px-4 py-3 text-end">Amount</th>
										<th class="px-4 py-3 text-end">Action</th>
									</tr>
								</thead>
								<tbody>
									@php
										$navigatorRows = collect($orderNavigator ?? collect())->filter(function ($o) {
											$status = strtolower((string) data_get($o, 'status', ''));
											return in_array($status, ['assigned', 'pending', 'picked', 'out for delivery', 'out_for_delivery', 'delivered', 'failed', 'returned'], true);
										});
									@endphp
									@forelse($navigatorRows as $navOrder)
										<tr @if((int) $navOrder->id === (int) $order->id) style="background:#fff7f3;" @endif>
											<td class="px-4 py-3">
												<span class="badge badge-primary light">{{ $navOrder->order_number ?? ('#' . $navOrder->id) }}</span>
											</td>
											<td class="px-4 py-3">{{ data_get($navOrder, 'store.store_name', 'Store N/A') }}</td>
											<td class="px-4 py-3 text-center">
												<span class="badge
													@if(($navOrder->status ?? '') === 'Delivered') bg-success
													@elseif(($navOrder->status ?? '') === 'Out for Delivery') bg-warning
													@elseif(in_array(($navOrder->status ?? ''), ['Failed','Returned'])) bg-danger
													@else bg-info @endif">
													{{ $navOrder->status ?? 'Pending' }}
												</span>
											</td>
											<td class="px-4 py-3 text-end">Rs {{ number_format((float) ($navOrder->total_amount ?? $navOrder->amount ?? 0), 2) }}</td>
											<td class="px-4 py-3 text-end">
												<button
													type="button"
													class="btn btn-sm btn-primary js-order-open-modal"
													data-order-url="{{ route('delivery.panel.order.details', ['id' => $navOrder->id, 'modal' => 1]) }}#storeDetailsSection"
													@if((int) $navOrder->id === (int) $order->id) disabled @endif
												>
													Open
												</button>
											</td>
										</tr>
									@empty
										<tr>
											<td class="px-4 py-3 text-center text-muted" colspan="5">No assigned/picked orders found.</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="storeDetailsSection" class="card mb-4 store-card">
					<div class="card-header border-0">
						<h5 class="card-title">Store Details</h5>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6 mb-3">
								<p class="detail-label">Store Name</p>
								<h6 class="detail-value-sm">{{ data_get($order, 'store.store_name', 'Store N/A') }}</h6>
							</div>
							<div class="col-md-6 mb-3">
								<p class="detail-label">Store Owner</p>
								<h6 class="detail-value-sm">{{ data_get($order, 'store.manager', data_get($order, 'store.contacts.0.contact_person', 'N/A')) }}</h6>
							</div>
							<div class="col-md-6 mb-3">
								<p class="detail-label">Contact Number</p>
								<h6 class="detail-value-sm"><a href="tel:{{ data_get($order, 'store.phone', data_get($order, 'customer_phone', '')) }}">{{ data_get($order, 'store.phone', data_get($order, 'customer_phone', 'N/A')) }}</a></h6>
							</div>
							<div class="col-md-6 mb-3">
								<p class="detail-label">Alternate Number</p>
								<h6 class="detail-value-sm"><a href="tel:{{ data_get($order, 'customer_phone', data_get($order, 'store.contacts.0.phone', '')) }}">{{ data_get($order, 'customer_phone', data_get($order, 'store.contacts.0.phone', 'N/A')) }}</a></h6>
							</div>
							<div class="col-12 mb-0">
								<p class="detail-label">Address</p>
								<h6 class="detail-value-sm">{{ data_get($order, 'store.address', 'N/A') }}</h6>
							</div>
						</div>
					</div>

				@php
					$storeLat      = data_get($order, 'store.latitude');
					$storeLng      = data_get($order, 'store.longitude');
					$storeAddr     = data_get($order, 'store.address');
					$storePhone    = data_get($order, 'store.phone');
					$storeManager  = data_get($order, 'store.manager');
					$storeCityName = data_get($order, 'store.city.name');
					$storeMap      = ($storeLat && $storeLng)
						? 'https://maps.google.com/?q='.$storeLat.','.$storeLng
						: ($storeAddr ? 'https://maps.google.com/?q='.urlencode($storeAddr) : null);
					$storeDirections = ($storeLat && $storeLng)
						? 'https://www.google.com/maps/dir/?api=1&destination='.$storeLat.','.$storeLng
						: ($storeAddr ? 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($storeAddr) : null);
					$salesPersonName = data_get($order, 'createdBy.name', data_get($order, 'salesPerson.name', null));
				@endphp

				<div class="card mb-4" style="border:2px solid #f73a0b;border-radius:14px;overflow:hidden;">
					<div class="card-header border-0 d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg,#f73a0b,#ff6b35);padding:14px 20px;">
						<h5 class="card-title mb-0 text-white"><i class="fas fa-map-marker-alt me-2"></i>Delivery Location</h5>
						@if($storeMap)
						<a href="{{ $storeMap }}" target="_blank" style="color:#fff;font-size:12px;opacity:.85;text-decoration:none;">
							<i class="fas fa-external-link-alt me-1"></i>Open Map
						</a>
						@endif
					</div>
					<div class="card-body" style="padding:16px 20px;">

						{{-- Store Name & Contact --}}
						<div style="background:#fff7f3;border-radius:10px;padding:12px 14px;margin-bottom:12px;">
							<div style="font-size:16px;font-weight:700;color:#1a1a1a;margin-bottom:4px;">
								<i class="fas fa-store me-2" style="color:#f73a0b;"></i>{{ data_get($order, 'store.store_name', 'N/A') }}
							</div>
							@if($storeManager)
							<div style="font-size:13px;color:#6b7280;margin-bottom:2px;">
								<i class="fas fa-user me-1"></i>{{ $storeManager }}
							</div>
							@endif
							@if($storePhone)
							<a href="tel:{{ $storePhone }}" style="font-size:13px;color:#2563eb;font-weight:600;text-decoration:none;">
								<i class="fas fa-phone me-1"></i>{{ $storePhone }}
							</a>
							@endif
						</div>

						{{-- Address & City --}}
						<div style="background:#f8fafc;border-radius:10px;padding:12px 14px;margin-bottom:12px;">
							<div style="font-size:12px;color:#9ca3af;font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;">Delivery Address</div>
							<div style="font-size:14px;color:#374151;font-weight:500;">
								<i class="fas fa-map-pin me-1" style="color:#f73a0b;"></i>
								{{ $storeAddr ?? 'Address not set' }}
								@if($storeCityName)
								<span style="color:#6b7280;">, {{ $storeCityName }}</span>
								@endif
							</div>
						</div>

						{{-- Sales Person --}}
						@if($salesPersonName)
						<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:10px 14px;margin-bottom:12px;">
							<div style="font-size:12px;color:#9ca3af;font-weight:600;margin-bottom:2px;text-transform:uppercase;letter-spacing:.5px;">Order Issued By</div>
							<div style="font-size:13px;color:#15803d;font-weight:600;">
								<i class="fas fa-user-tie me-1"></i>{{ $salesPersonName }}
							</div>
						</div>
						@endif

						{{-- GPS Coordinates --}}
						@if($storeLat && $storeLng)
						<div style="background:#eff6ff;border-radius:10px;padding:10px 14px;margin-bottom:14px;">
							<div style="font-size:12px;color:#9ca3af;font-weight:600;margin-bottom:2px;text-transform:uppercase;letter-spacing:.5px;">GPS Coordinates</div>
							<div style="font-size:12px;color:#1d4ed8;font-family:monospace;">
								<i class="fas fa-crosshairs me-1"></i>{{ $storeLat }}, {{ $storeLng }}
							</div>
						</div>
						@endif

						{{-- Action Buttons --}}
						<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
							@if($storeDirections)
							<a href="{{ $storeDirections }}" target="_blank"
							   style="display:flex;align-items:center;justify-content:center;gap:6px;background:#f73a0b;color:#fff;border-radius:10px;padding:12px 8px;font-weight:700;font-size:14px;text-decoration:none;">
								<i class="fas fa-route"></i> Get Directions
							</a>
							@endif
							@if($storePhone)
							<a href="tel:{{ $storePhone }}"
							   style="display:flex;align-items:center;justify-content:center;gap:6px;background:#16a34a;color:#fff;border-radius:10px;padding:12px 8px;font-weight:700;font-size:14px;text-decoration:none;">
								<i class="fas fa-phone"></i> Call Store
							</a>
							@endif
							@if(!$storeDirections && !$storePhone)
							<div class="col-span-2" style="grid-column:span 2;">
								<p class="text-muted small mb-0 text-center"><i class="fas fa-info-circle me-1"></i>No GPS or phone set. Ask admin to update store details.</p>
							</div>
							@endif
						</div>

					</div>
				</div>

				</div>

				<div class="card mb-4">
					<div class="card-header border-0">
						<h5 class="card-title">Order Items</h5>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-striped mb-0">
								<thead>
									<tr class="border-top">
										<th class="px-4 py-3">Product</th>
										<th class="px-4 py-3 text-center">Qty</th>
										<th class="px-4 py-3 text-center">Stock</th>
										<th class="px-4 py-3 text-end px-4">Amount</th>
									</tr>
								</thead>
								<tbody>
									@forelse($orderItems as $item)
									@php
										$currentStock = (int) data_get($item, 'product.stock_quantity', 0);
										$isLowStock = $currentStock <= 10;
									@endphp
									<tr>
										<td class="px-4 py-3">
											<p class="mb-0 fw-bold">{{ data_get($item, 'product.product_name', $item->product_name ?? 'Product') }}</p>
											<small class="text-muted">SKU: {{ data_get($item, 'product.sku', 'N/A') }}</small>
										</td>
										<td class="px-4 py-3 text-center">{{ $item->quantity ?? 0 }}</td>
										<td class="px-4 py-3 text-center">
											<span class="badge {{ $isLowStock ? 'bg-danger' : 'bg-success' }}">{{ $currentStock }}</span>
										</td>
										<td class="px-4 py-3 text-end">Rs {{ number_format((float) ($item->subtotal ?? $item->total ?? (($item->quantity ?? 0) * ($item->unit_price ?? $item->price ?? 0))), 2) }}</td>
									</tr>
									@empty
									<tr>
										<td class="px-4 py-3 text-center text-muted" colspan="4">No order items found.</td>
									</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="card mb-4">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
							<p class="text-muted mb-0">Subtotal:</p>
							<h6 class="mb-0 fw-bold">Rs {{ number_format($computedSubtotal, 2) }}</h6>
						</div>
						<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
							<p class="text-muted mb-0">Discount:</p>
							<h6 class="mb-0 fw-bold text-danger">-Rs {{ number_format($discountAmount, 2) }}</h6>
						</div>
						<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
							<p class="text-muted mb-0">Shipping:</p>
							<h6 class="mb-0 fw-bold">Rs {{ number_format($shippingAmount, 2) }}</h6>
						</div>
						<div class="d-flex justify-content-between align-items-center">
							<p class="mb-0 fw-bold fs-5">Total Amount:</p>
							<h5 class="mb-0 fw-bold text-primary" style="color: #f73a0b !important;">Rs {{ number_format($orderTotal, 2) }}</h5>
						</div>
					</div>
				</div>

				<div class="card mb-4">
					<div class="card-header border-0">
						<h5 class="card-title">Order Status Timeline</h5>
					</div>
					<div class="card-body">
						<div class="timeline">
							@forelse(($statusTimeline ?? collect()) as $event)
							<div class="timeline-item mb-4 pb-3 border-bottom">
								<div class="d-flex align-items-start">
									<div class="timeline-badge bg-{{ $event['badge'] ?? 'secondary' }} text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
										<i class="fas {{ $event['icon'] ?? 'fa-circle' }}"></i>
									</div>
									<div class="flex-grow-1">
										<h6 class="mb-1">{{ $event['label'] ?? 'Status Update' }}</h6>
										<p class="text-muted mb-0 fs-12">{{ optional($event['time'] ?? null)->format('d M Y, h:i A') ?? 'N/A' }}</p>
										@if(!empty($event['description']))
											<small class="text-muted">{{ $event['description'] }}</small>
										@endif
									</div>
								</div>
							</div>
							@empty
							<div class="timeline-item">
								<div class="d-flex align-items-start">
									<div class="timeline-badge bg-secondary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
										<i class="fas fa-circle fs-6"></i>
									</div>
									<div class="flex-grow-1">
										<h6 class="mb-1">No timeline events yet</h6>
										<p class="text-muted mb-0 fs-12">Order updates will appear here.</p>
									</div>
								</div>
							</div>
							@endforelse
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-body">
						<h6 class="card-title mb-3">Update Order Status</h6>
						<form method="POST" action="{{ route('delivery.panel.orders.status', ['order' => $order->id]) }}" enctype="multipart/form-data">
							@csrf
							@if($isModalView)
								<input type="hidden" name="modal" value="1">
							@endif
							<div class="mb-3">
								<label class="form-label">Select New Status</label>
								<select class="form-select form-control" name="status" id="statusSelect" required>
									<option value="">Choose Status...</option>
									<option value="Assigned" @selected(($order->status ?? '') === 'Assigned')>Assigned</option>
									<option value="Picked" @selected(($order->status ?? '') === 'Picked')>Mark as Picked</option>
									<option value="Out for Delivery" @selected(($order->status ?? '') === 'Out for Delivery')>Out for Delivery</option>
									<option value="Delivered" @selected(($order->status ?? '') === 'Delivered')>Mark as Delivered</option>
									<option value="Failed" @selected(($order->status ?? '') === 'Failed')>Failed Delivery</option>
									<option value="Returned" @selected(($order->status ?? '') === 'Returned')>Returned</option>
								</select>
							</div>
							
				<!-- Delivery Proof: GPS + Live Photo (shown only for Delivered) -->
				<div id="deliveryProofSection" class="mb-3 p-3 border rounded" style="display:none;background:#f8fff8;">
					<p class="fw-semibold mb-2"><i class="fas fa-shield-alt text-success me-1"></i> Delivery Proof Required</p>
					<div class="mb-3">
						<label class="form-label"><i class="fas fa-map-marker-alt text-danger me-1"></i> Capture Delivery Location</label>
						<input type="hidden" name="delivery_lat" id="deliveryLat">
						<input type="hidden" name="delivery_lng" id="deliveryLng">
						<div class="d-flex align-items-center gap-2 flex-wrap">
							<button type="button" class="btn btn-outline-danger btn-sm" id="captureLocationBtn"><i class="fas fa-crosshairs me-1"></i> Get My Location</button>
							<span id="locationStatus" class="small text-muted">Not captured</span>
						</div>
						<a id="mapLink" href="#" target="_blank" class="small text-success mt-1 d-none"><i class="fas fa-external-link-alt me-1"></i>View on Map</a>
					</div>
					<div class="mb-2">
						<label class="form-label"><i class="fas fa-camera text-primary me-1"></i> Live Photo of Delivery Person</label>
						<div class="mb-2 d-flex gap-2 flex-wrap">
							<button type="button" class="btn btn-outline-primary btn-sm" id="openCameraBtn"><i class="fas fa-video me-1"></i> Open Camera</button>
							<button type="button" class="btn btn-outline-success btn-sm d-none" id="snapPhotoBtn"><i class="fas fa-camera me-1"></i> Take Photo</button>
							<button type="button" class="btn btn-outline-secondary btn-sm d-none" id="retakePhotoBtn"><i class="fas fa-redo me-1"></i> Retake</button>
						</div>
						<video id="cameraStream" autoplay playsinline class="d-none rounded" style="width:100%;max-width:300px;border:1px solid #ddd;"></video>
						<canvas id="photoCanvas" class="d-none rounded" style="width:100%;max-width:300px;border:1px solid #ddd;"></canvas>
						<input type="file" name="delivery_photo" id="deliveryPhotoInput" accept="image/*" class="d-none">
						<p id="photoStatus" class="small text-muted mt-1 mb-0">No photo taken</p>
					</div>
				</div>

				<!-- Delivery Proof: GPS + Live Photo (shown only for Delivered) -->
				<div id="deliveryProofSection" class="mb-3 p-3 border rounded" style="display:none;background:#f8fff8;">
					<p class="fw-semibold mb-2"><i class="fas fa-shield-alt text-success me-1"></i> Delivery Proof Required</p>
					<div class="mb-3">
						<label class="form-label"><i class="fas fa-map-marker-alt text-danger me-1"></i> Capture Delivery Location</label>
						<input type="hidden" name="delivery_lat" id="deliveryLat">
						<input type="hidden" name="delivery_lng" id="deliveryLng">
						<div class="d-flex align-items-center gap-2 flex-wrap">
							<button type="button" class="btn btn-outline-danger btn-sm" id="captureLocationBtn"><i class="fas fa-crosshairs me-1"></i> Get My Location</button>
							<span id="locationStatus" class="small text-muted">Not captured</span>
						</div>
						<a id="mapLink" href="#" target="_blank" class="small text-success mt-1 d-none"><i class="fas fa-external-link-alt me-1"></i>View on Map</a>
					</div>
					<div class="mb-2">
						<label class="form-label"><i class="fas fa-camera text-primary me-1"></i> Live Photo of Delivery Person</label>
						<div class="mb-2 d-flex gap-2 flex-wrap">
							<button type="button" class="btn btn-outline-primary btn-sm" id="openCameraBtn"><i class="fas fa-video me-1"></i> Open Camera</button>
							<button type="button" class="btn btn-outline-success btn-sm d-none" id="snapPhotoBtn"><i class="fas fa-camera me-1"></i> Take Photo</button>
							<button type="button" class="btn btn-outline-secondary btn-sm d-none" id="retakePhotoBtn"><i class="fas fa-redo me-1"></i> Retake</button>
						</div>
						<video id="cameraStream" autoplay playsinline class="d-none rounded" style="width:100%;max-width:300px;border:1px solid #ddd;"></video>
						<canvas id="photoCanvas" class="d-none rounded" style="width:100%;max-width:300px;border:1px solid #ddd;"></canvas>
						<input type="file" name="delivery_photo" id="deliveryPhotoInput" accept="image/*" class="d-none">
						<p id="photoStatus" class="small text-muted mt-1 mb-0">No photo taken</p>
					</div>
				</div>
<div class="mb-3" id="reasonDiv" style="display: none;">
								<label class="form-label">Failure Reason</label>
								<select class="form-select form-control" name="failure_reason">
									<option value="">Select Reason...</option>
									<option value="Owner Not Available">Owner Not Available</option>
									<option value="Wrong Location">Wrong Location</option>
									<option value="Order Cancelled">Order Cancelled</option>
									<option value="Payment Failed">Payment Failed</option>
									<option value="Out of Stock">Out of Stock</option>
									<option value="Customer Rejected">Customer Rejected</option>
								</select>
							</div>
							<div class="mb-3">
								<label class="form-label">Notes</label>
								<textarea class="form-control" rows="3" name="notes" placeholder="Add any additional notes...">{{ $order->notes ?? '' }}</textarea>
							</div>
							<button type="submit" class="btn btn-primary w-100" style="background-color: #f73a0b; border-color: #f73a0b;">
								<i class="fas fa-save me-2"></i> Update Status
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--**********************************
    Content body end
***********************************-->

<div class="modal fade" id="orderDetailPopup" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Order Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-0" style="height: 80vh;">
				<iframe id="orderDetailPopupFrame" src="about:blank" style="width:100%;height:100%;border:0;" loading="lazy"></iframe>
			</div>
		</div>
	</div>
</div>

		
		

		
        		<!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy; {{ $panelCompanyName }} - Delivery Panel <span class="current-year">2023</span></p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->



	</div>
    
    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('deliver_assets/vendor/global/global.min.js') }}"></script>
	<script src="{{ asset('deliver_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
   	<script src="{{ asset('deliver_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>

    <script src="{{ asset('deliver_assets/js/custom.min.js') }}"></script>
	<script src="{{ asset('deliver_assets/js/dlabnav-init.js') }}"></script>
	<script src="{{ asset('deliver_assets/js/demo.js') }}"></script>
	<!-- localizationTool -->
	<script src="{{ asset('deliver_assets/js/jquery.localizationTool.js') }}"></script>
	<script src="{{ asset('deliver_assets/js/translator.js') }}"></script>	
	
	<script>
		const statusSelect = document.getElementById('statusSelect');
		const reasonDiv = document.getElementById('reasonDiv');
		const deliveryProofSection = document.getElementById('deliveryProofSection');
		if (statusSelect) {
			const toggleFields = function () {
				const v = statusSelect.value;
				if (reasonDiv) reasonDiv.style.display = (v === 'Failed' || v === 'Returned') ? 'block' : 'none';
				if (deliveryProofSection) deliveryProofSection.style.display = (v === 'Delivered') ? 'block' : 'none';
			};
			statusSelect.addEventListener('change', toggleFields);
			toggleFields();
		}

		// GPS Location capture
		const captureLocationBtn = document.getElementById('captureLocationBtn');
		const locationStatus = document.getElementById('locationStatus');
		const mapLink = document.getElementById('mapLink');
		if (captureLocationBtn) {
			captureLocationBtn.addEventListener('click', function () {
				if (!navigator.geolocation) { locationStatus.textContent = 'Geolocation not supported.'; return; }
				locationStatus.textContent = 'Fetching location...';
				navigator.geolocation.getCurrentPosition(function (pos) {
					const lat = pos.coords.latitude.toFixed(7);
					const lng = pos.coords.longitude.toFixed(7);
					document.getElementById('deliveryLat').value = lat;
					document.getElementById('deliveryLng').value = lng;
					locationStatus.textContent = '\u2705 ' + lat + ', ' + lng;
					locationStatus.className = 'small text-success';
					mapLink.href = 'https://maps.google.com/?q=' + lat + ',' + lng;
					mapLink.classList.remove('d-none');
				}, function () {
					locationStatus.textContent = 'Unable to get location. Please allow access.';
					locationStatus.className = 'small text-danger';
				});
			});
		}

		// Camera capture
		let cameraStream = null;
		const video = document.getElementById('cameraStream');
		const canvas = document.getElementById('photoCanvas');
		const photoInput = document.getElementById('deliveryPhotoInput');
		const photoStatus = document.getElementById('photoStatus');
		const openCameraBtn = document.getElementById('openCameraBtn');
		const snapPhotoBtn = document.getElementById('snapPhotoBtn');
		const retakePhotoBtn = document.getElementById('retakePhotoBtn');

		if (openCameraBtn) {
			openCameraBtn.addEventListener('click', function () {
				navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
					.then(function (stream) {
						cameraStream = stream;
						video.srcObject = stream;
						video.classList.remove('d-none');
						snapPhotoBtn.classList.remove('d-none');
						openCameraBtn.classList.add('d-none');
						canvas.classList.add('d-none');
						photoStatus.textContent = 'Camera ready. Take a photo.';
					}).catch(function () {
						photoStatus.textContent = 'Camera access denied.';
					});
			});
		}

		if (snapPhotoBtn) {
			snapPhotoBtn.addEventListener('click', function () {
				canvas.width = video.videoWidth;
				canvas.height = video.videoHeight;
				canvas.getContext('2d').drawImage(video, 0, 0);
				canvas.classList.remove('d-none');
				video.classList.add('d-none');
				snapPhotoBtn.classList.add('d-none');
				retakePhotoBtn.classList.remove('d-none');
				if (cameraStream) { cameraStream.getTracks().forEach(t => t.stop()); cameraStream = null; }
				canvas.toBlob(function (blob) {
					const file = new File([blob], 'delivery_photo.jpg', { type: 'image/jpeg' });
					const dt = new DataTransfer();
					dt.items.add(file);
					photoInput.files = dt.files;
					photoStatus.textContent = '\u2705 Photo captured.';
					photoStatus.className = 'small text-success mt-1 mb-0';
				}, 'image/jpeg', 0.85);
			});
		}

		if (retakePhotoBtn) {
			retakePhotoBtn.addEventListener('click', function () {
				canvas.classList.add('d-none');
				retakePhotoBtn.classList.add('d-none');
				openCameraBtn.classList.remove('d-none');
				photoInput.value = '';
				photoStatus.textContent = 'No photo taken';
				photoStatus.className = 'small text-muted mt-1 mb-0';
			});
		}

		function removeThirdPartyWidgets() {
			var selectors = [
				'a[href*="support.w3itexperts"]',
				'a[href*="envato.market"]',
				'.sidebar-right',
				'.sidebar-right-trigger',
				'.sidebar-close-trigger',
				'.dlab-demo-panel',
				'.dlab-demo-trigger',
				'#DZ_THEME_PANEL',
				'#DZScript'
			];
			selectors.forEach(function (selector) {
				document.querySelectorAll(selector).forEach(function (el) {
					el.remove();
				});
			});
			document.querySelectorAll('a,button,div,span').forEach(function (el) {
				var text = (el.textContent || '').trim().toUpperCase();
				if (text === 'SUPPORT' || text === 'BUY NOW' || text.indexOf('PICK YOUR STYLE') !== -1 || text.indexOf('DELETE ALL COOKIE') !== -1) {
					el.remove();
				}
			});
		}
		removeThirdPartyWidgets();
		setInterval(removeThirdPartyWidgets, 500);
		new MutationObserver(removeThirdPartyWidgets).observe(document.body, { childList: true, subtree: true });

		(function () {
			const popup = document.getElementById('orderDetailPopup');
			const frame = document.getElementById('orderDetailPopupFrame');
			if (!popup || !frame || typeof bootstrap === 'undefined') return;

			const modal = new bootstrap.Modal(popup);
			document.querySelectorAll('.js-order-open-modal').forEach(function (btn) {
				btn.addEventListener('click', function () {
					const url = btn.getAttribute('data-order-url');
					if (!url) return;
					frame.src = url;
					modal.show();
				});
			});

			frame.addEventListener('load', function () {
				try {
					const doc = frame.contentWindow?.document;
					const target = doc?.getElementById('storeDetailsSection');
					frame.contentWindow?.scrollTo(0, 0);
					if (target) {
						target.scrollIntoView({ block: 'start' });
					}
				} catch (e) {
					// ignore cross-frame edge cases
				}
			});

			popup.addEventListener('hidden.bs.modal', function () {
				frame.src = 'about:blank';
			});
		})();
	</script>
	
</body>
</html>
