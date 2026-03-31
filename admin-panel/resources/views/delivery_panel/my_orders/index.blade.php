<!DOCTYPE html>
<html lang="en">
<head>

   <!-- PAGE TITLE HERE -->
	<title><?php echo e(preg_replace('/sales\s*panel/i', 'Delivery Panel', (string) ($companyName ?? 'Delivery Panel')) ?: ($companyName ?? 'Delivery Panel')); ?> My Orders</title>

    <!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	

	<!-- Canonical URL -->
	<link rel="canonical" href="new-job.html">

	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Favicon icon -->
	<link rel="shortcut icon" type="image/png" href="<?php echo e(asset('assets/images/favicon.png')); ?>">
	<link href="<?php echo e(asset('deliver_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('deliver_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css')); ?>" rel="stylesheet">
	
	<!-- Localization Tool -->
	<link href="<?php echo e(asset('deliver_assets/css/jquery.localizationTool.css')); ?>" rel="stylesheet">
	
	<!-- Style Css -->
	<link class="main-css" href="<?php echo e(asset('deliver_assets/css/style.css')); ?>" rel="stylesheet">
	<style>
		/* Remove third-party floating support/buy widgets */
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

		/* Keep action buttons readable on hover/focus */
		.table .btn.btn-primary:hover,
		.table .btn.btn-primary:focus,
		.table .btn.btn-primary:active,
		.table .btn.btn-info:hover,
		.table .btn.btn-info:focus,
		.table .btn.btn-info:active,
		.table .btn.btn-success:hover,
		.table .btn.btn-success:focus,
		.table .btn.btn-success:active,
		.table .btn.btn-danger:hover,
		.table .btn.btn-danger:focus,
		.table .btn.btn-danger:active {
			color: #fff !important;
			filter: brightness(0.95);
		}

		/* Keep primary action button color stable in assigned tab */
		.table .btn.btn-primary,
		.table .btn.btn-primary:hover,
		.table .btn.btn-primary:focus,
		.table .btn.btn-primary:active {
			background-color: #f73a0b !important;
			border-color: #f73a0b !important;
			color: #fff !important;
		}

		/* Remove preloader from My Orders page */
		#preloader {
			display: none !important;
			visibility: hidden !important;
		}
	</style>
	

</head>
<body>
    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        <?php
            if (!isset($orders) || empty($orders)) {
                $orders = \App\Models\Order::with('store')->latest()->get();
            }
            $recentOrders = method_exists($orders, 'getCollection') ? $orders->getCollection() : collect($orders ?? []);
            $profileImage = asset('deliver_assets/images/profile/pic1.jpg');
            if (!empty($companySettings?->profile_image)) {
                $logoPath = ltrim((string) $companySettings->profile_image, '/');
                if (str_starts_with($logoPath, 'http')) {
                    $profileImage = $logoPath;
                } elseif (file_exists(public_path('uploads/admin/' . $logoPath))) {
                    $profileImage = asset('uploads/admin/' . $logoPath);
                } elseif (file_exists(public_path('storage/' . $logoPath))) {
                    $profileImage = asset('storage/' . $logoPath);
                }
            }
            $companyLogo = $profileImage;
            $panelCompanyName = preg_replace('/sales\s*panel/i', 'Delivery Panel', (string) ($companyName ?? 'Delivery Panel')) ?: ($companyName ?? 'Delivery Panel');
            $deliveryUserName = data_get($deliveryProfile, 'name', data_get($user, 'name', 'Delivery Executive'));
            $deliveryUserRole = ucfirst((string) data_get($user, 'role', 'delivery'));
            if (!empty($deliveryProfile?->avatar_path)) {
                $avatarPath = ltrim((string) $deliveryProfile->avatar_path, '/');
                if (str_starts_with($avatarPath, 'http')) {
                    $profileImage = $avatarPath;
                } elseif (file_exists(public_path('storage/' . $avatarPath))) {
                    $profileImage = asset('storage/' . $avatarPath);
                } elseif (file_exists(public_path('uploads/admin/' . $avatarPath))) {
                    $profileImage = asset('uploads/admin/' . $avatarPath);
                }
            }
            $settingsSummary = collect($statusCounts ?? [])
                ->filter(fn ($count) => (int) $count > 0)
                ->take(8);
        ?>

        		<!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="{{ route('delivery.panel.my.orders') }}" class="brand-logo">
                <img class="logo-abbr" src="<?php echo e($companyLogo); ?>" alt="<?php echo e($panelCompanyName); ?>" style="width:42px;height:42px;object-fit:cover;border-radius:10px;">
                <div class="brand-title">
                    <h2 class="mb-0"><?php echo e($panelCompanyName); ?></h2>
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
					<?php echo e($panelCompanyName); ?> Orders Dashboard
					</div>
								<div class="nav-item d-flex align-items-center">
									<form action="index.html">
										<div class="input-group search-area">
											<input type="text" class= placeholder="Search">
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
								

								<!-- Notifications (Sales events only) -->
								<li class="nav-item dropdown notification_dropdown">
					<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24">
										  <g data-name="Layer 2" transform="translate(-2 -2)">
											<path id="Path_20" data-name="Path 20" d="M22.571,15.8V13.066a8.5,8.5,0,0,0-7.714-8.455V2.857a.857.857,0,0,0-1.714,0V4.611a8.5,8.5,0,0,0-7.714,8.455V15.8A4.293,4.293,0,0,0,2,20a2.574,2.574,0,0,0,2.571,2.571H9.8a4.286,4.286,0,0,0,8.4,0h5.23A2.574,2.574,0,0,0,26,20,4.293,4.293,0,0,0,22.571,15.8ZM7.143,13.066a6.789,6.789,0,0,1,6.78-6.78h.154a6.789,6.789,0,0,1,6.78,6.78v2.649H7.143ZM14,24.286a2.567,2.567,0,0,1-2.413-1.714h4.827A2.567,2.567,0,0,1,14,24.286Zm9.429-3.429H4.571A.858.858,0,0,1,3.714,20a2.574,2.574,0,0,1,2.571-2.571H21.714A2.574,2.574,0,0,1,24.286,20a.858.858,0,0,1-.857.857Z"></path>
										  </g>
										</svg>
						<span class="badge light text-white bg-primary rounded-circle"><?php echo e($recentOrders->take(9)->count()); ?></span>
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:380px;">
											<ul class="timeline">
											<?php $__empty_1 = true; $__currentLoopData = $recentOrders->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
												<?php
													$status = (string) ($order->status ?? 'Order');
													$badgeClass = match ($status) {
														'Delivered' => 'success',
														'Returned', 'Failed' => 'danger',
														'Out for Delivery' => 'info',
														'Picked' => 'warning',
														'Assigned', 'Pending' => 'primary',
														default => 'secondary',
													};
												?>
												<li>
													<div class="timeline-panel">
														<div class="media me-2">
															<span class="badge bg-<?php echo e($badgeClass); ?> text-white"><?php echo e($status); ?></span>
														</div>
														<div class="media-body">
															<h6 class="mb-1">Order <?php echo e($order->order_number ?? '#N/A'); ?> - <?php echo e(data_get($order, 'store.store_name', 'Store N/A')); ?></h6>
															<small class="d-block"><?php echo e(optional($order->created_at)->diffForHumans() ?? 'Just now'); ?></small>
														</div>
													</div>
												</li>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
												<li>
													<div class="timeline-panel">
														<div class="media-body">
															<h6 class="mb-1">No notifications yet</h6>
															<small class="d-block">Orders will appear here once assigned.</small>
														</div>
													</div>
												</li>
											<?php endif; ?>
</ul>
										</div>
						<a class="all-notification" href="<?php echo e(route('delivery.panel.my.orders')); ?>">See all notifications</a>
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

										<span class="badge light text-white bg-primary rounded-circle"><?php echo e($settingsSummary->count()); ?></span>
					</a>
									<div class="dropdown-menu dropdown-menu-end">
										<div id="DZ_W_TimeLine02" class="widget-timeline dlab-scroll style-1 p-3 height370">
						<ul class="timeline">
						<?php $__empty_1 = true; $__currentLoopData = $settingsSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusName => $statusCount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
							<?php
								$timelineClass = match ((string) $statusName) {
									'Delivered' => 'success',
									'Out for Delivery' => 'warning',
									'Returned', 'Failed' => 'danger',
									'Assigned', 'Pending', 'Picked' => 'primary',
									default => 'dark',
								};
							?>
							<li>
								<div class="timeline-badge <?php echo e($timelineClass); ?>"></div>
								<a class="timeline-panel text-muted" href="<?php echo e(route('delivery.panel.my.orders')); ?>">
									<span><?php echo e(now()->diffForHumans()); ?></span>
									<h6 class="mb-0"><?php echo e($statusName); ?> Orders: <?php echo e((int) $statusCount); ?></h6>
								</a>
							</li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
							<li>
								<div class="timeline-badge dark"></div>
								<a class="timeline-panel text-muted" href="javascript:void(0);">
									<span>No recent activity</span>
									<h6 class="mb-0">New order updates will appear here.</h6>
								</a>
							</li>
						<?php endif; ?>
</ul>
					</div>
									</div>
								</li>

								<!-- User Profile -->
								<li class="nav-item dropdown header-profile">
					<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
						<img src="<?php echo e($profileImage); ?>" width="20" alt="<?php echo e($deliveryUserName); ?>">
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<a href="<?php echo e(route('delivery.panel.profile')); ?>" class="dropdown-item ai-icon">
						<svg id="icon-user2" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
						<span class="ms-2">My Profile</span>
						</a>
						<a href="javascript:void(0);" class="dropdown-item ai-icon" onclick="if(confirm('Are you sure you want to logout?')){ event.preventDefault(); document.getElementById('deliveryLogoutForm').submit(); }">
						<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
						<span class="ms-2">Logout</span>
						</a>
					</div>
					</li>
				</ul>
                <form id="deliveryLogoutForm" action="<?php echo e(route('delivery.panel.logout')); ?>" method="POST" class="d-none">
                    <?php echo csrf_field(); ?>
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
							<img src="<?php echo e($profileImage); ?>" alt="<?php echo e($deliveryUserName); ?>">
							<div class="d-flex align-items-center sidebar-info">
								<div>
									<span class="font-w400 d-block"><?php echo e($deliveryUserName); ?></span>
									<small class="text-end font-w400"><?php echo e($deliveryUserRole); ?></small>
								</div>	
								<i class="fas fa-chevron-down"></i>
							</div>
							
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<a href="app-profile.html" class="dropdown-item ai-icon ">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
							<span class="ms-2">Profile </span>
						</a>
						<a href="email-inbox.html" class="dropdown-item ai-icon">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
							<span class="ms-2">Inbox </span>
						</a>
						<a href="page-register.html" class="dropdown-item ai-icon">
							<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
							<span class="ms-2">Logout </span>
						</a>
					</div>
				</div>
				<ul class="metismenu" id="menu">
					<li>
						<a class="has-arrow" href="<?php echo e(route('delivery.panel.dashboard')); ?>" aria-expanded="false">
							<i class="flaticon-025-dashboard"></i>
							<span class="nav-text">Dashboard</span>
						</a>
					</li>
					<li>
						<a class="has-arrow" href="<?php echo e(route('delivery.panel.my.orders')); ?>" aria-expanded="false">
							<i class="flaticon-381-user-7"></i>
							<span class="nav-text">Orders</span>
						</a>
					</li>
					<li>
						<a class="has-arrow" href="<?php echo e(route('delivery.panel.order.details')); ?>" aria-expanded="false">
							<i class="flaticon-381-notepad"></i>
							<span class="nav-text">Order Details</span>
						</a>
					</li>
					<li>
						<a class="has-arrow" href="<?php echo e(route('delivery.panel.profile')); ?>" aria-expanded="false">
							<i class="flaticon-381-internet"></i>
							<span class="nav-text">Profile</span>
						</a>
					</li>
				</ul>

				<div class="plus-box">
					<p class="fs-14 font-w600 mb-2">Let <?php echo e($companyName ?? 'Delivery Panel'); ?> simplify<br>your delivery workflow</p>
					<p class="plus-box-p">Manage deliveries, orders, and reports in one place</p>
				</div>
				<div class="copyright">
					<p><strong><?php echo e($companyName ?? 'Delivery Panel'); ?></strong> - Delivery Panel &copy; <span class="current-year">2023</span></p>
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
		<!-- Header -->
		<div class="d-flex align-items-center mb-4">
			<h3 class="mb-0 me-auto">My Orders</h3>
		</div>

		<!-- Tabs Navigation -->
		<div class="row">
			<div class="col-xl-12">
				@php
					$rowsAssigned = collect(data_get($statusOrders ?? [], 'assigned', []));
					$rowsPicked = collect(data_get($statusOrders ?? [], 'picked', []));
					$rowsOut = collect(data_get($statusOrders ?? [], 'out_for_delivery', []));
					$rowsDelivered = collect(data_get($statusOrders ?? [], 'delivered', []));
					$rowsFailed = collect(data_get($statusOrders ?? [], 'failed', []));

					$normalizeStatus = function ($value) {
						$status = strtolower(trim((string) $value));
						return str_replace('_', ' ', $status);
					};

					if ($rowsAssigned->isEmpty()) {
						$rowsAssigned = $recentOrders->filter(function ($o) use ($normalizeStatus) {
							$status = $normalizeStatus(data_get($o, 'status', ''));
							return in_array($status, ['assigned', 'pending', 'processing'], true);
						})->values();
					}
					if ($rowsPicked->isEmpty()) {
						$rowsPicked = $recentOrders->filter(function ($o) use ($normalizeStatus) {
							return $normalizeStatus(data_get($o, 'status', '')) === 'picked';
						})->values();
					}
					if ($rowsOut->isEmpty()) {
						$rowsOut = $recentOrders->filter(function ($o) use ($normalizeStatus) {
							return $normalizeStatus(data_get($o, 'status', '')) === 'out for delivery';
						})->values();
					}
					if ($rowsDelivered->isEmpty()) {
						$rowsDelivered = $recentOrders->filter(function ($o) use ($normalizeStatus) {
							$status = $normalizeStatus(data_get($o, 'status', ''));
							return in_array($status, ['delivered', 'completed'], true);
						})->values();
					}
					if ($rowsFailed->isEmpty()) {
						$rowsFailed = $recentOrders->filter(function ($o) use ($normalizeStatus) {
							$status = $normalizeStatus(data_get($o, 'status', ''));
							return in_array($status, ['failed', 'returned', 'cancelled'], true);
						})->values();
					}

					$assignedCount = (int) $rowsAssigned->count();
					$pickedCount = (int) $rowsPicked->count();
					$outForDeliveryCount = (int) $rowsOut->count();
					$deliveredCount = (int) $rowsDelivered->count();
					$failedCount = (int) $rowsFailed->count();
				@endphp
				<div class="card">
					<div class="card-header border-0">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-bs-toggle="tab" href="#assigned" role="tab">
									<span class="d-block d-sm-none"><i class="fas fa-cube"></i></span>
									<span class="d-none d-sm-block">Assigned (<?php echo e($assignedCount); ?>)</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#picked" role="tab">
									<span class="d-block d-sm-none"><i class="fas fa-check"></i></span>
									<span class="d-none d-sm-block">Picked (<?php echo e($pickedCount); ?>)</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#outfordelivery" role="tab">
									<span class="d-block d-sm-none"><i class="fas fa-truck"></i></span>
									<span class="d-none d-sm-block">Out for Delivery (<?php echo e($outForDeliveryCount); ?>)</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#delivered" role="tab">
									<span class="d-block d-sm-none"><i class="fas fa-check-double"></i></span>
									<span class="d-none d-sm-block">Delivered (<?php echo e($deliveredCount); ?>)</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#failed" role="tab">
									<span class="d-block d-sm-none"><i class="fas fa-times"></i></span>
									<span class="d-none d-sm-block">Failed / Returned (<?php echo e($failedCount); ?>)</span>
								</a>
							</li>
						</ul>
					</div>

					<!-- Tab Content -->
					<div class="card-body p-0">
						<div class="tab-content">
							@php
								$fmtAmount = fn ($o) => (float) data_get($o, 'total_amount', data_get($o, 'amount', 0));
								$storeLocation = fn ($o) => (string) (data_get($o, 'store.address')
									?: data_get($o, 'store.locality')
									?: data_get($o, 'store.city')
									?: 'N/A');
								$storeMapUrl = function ($o) {
									$lat = data_get($o, 'store.latitude');
									$lng = data_get($o, 'store.longitude');
									if ($lat && $lng) return 'https://maps.google.com/?q=' . $lat . ',' . $lng;
									$addr = data_get($o, 'store.address');
									if ($addr) return 'https://maps.google.com/?q=' . urlencode($addr);
									return null;
								};
								$orderCode = fn ($o) => (string) (data_get($o, 'order_number') ?: ('#' . data_get($o, 'id')));
							@endphp

							<!-- Assigned Tab -->
							<div class="tab-pane fade show active" id="assigned" role="tabpanel">
								<div class="table-responsive">
									<table class="table table-hover mb-0">
										<thead class="table-light">
											<tr>
												<th>Order ID</th>
												<th>Store Name</th>
												<th>Location</th>
												<th>Amount</th>
												<th>Assign Time</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php $__empty_1 = true; $__currentLoopData = $rowsAssigned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
												<tr>
													<td><a href="<?php echo e(route('delivery.panel.order.details', ['id' => $orderRow->id])); ?>" class="badge badge-primary light"><?php echo e($orderCode($orderRow)); ?></a></td>
													<td><?php echo e(data_get($orderRow, 'store.store_name', 'Store N/A')); ?></td>
													<td>
														<?php echo e($storeLocation($orderRow)); ?>
														<?php $mapUrl = $storeMapUrl($orderRow); ?>
														<?php if($mapUrl): ?>
															<a href="<?php echo e($mapUrl); ?>" target="_blank" class="d-block mt-1 small text-primary">
																<i class="fas fa-map-marker-alt me-1"></i>View on Map
															</a>
														<?php endif; ?>
														<small class="d-block text-muted mt-1"><i class="fas fa-warehouse me-1"></i>Pick from Warehouse</small>
													</td>
													<td><strong>₹<?php echo e(number_format($fmtAmount($orderRow), 2)); ?></strong></td>
													<td><?php echo e(optional(data_get($orderRow, 'updated_at', data_get($orderRow, 'created_at')))->format('d-m-Y h:i A')); ?></td>
													<td>
														<form method="POST" action="<?php echo e(route('delivery.panel.orders.status', ['order' => $orderRow->id])); ?>">
															<?php echo csrf_field(); ?>
															<input type="hidden" name="status" value="Picked">
															<button class="btn btn-sm btn-primary" type="submit">
																<i class="fas fa-check me-2"></i>Mark as Picked
															</button>
														</form>
													</td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
												<tr><td colspan="6" class="text-center text-muted py-4">No assigned orders.</td></tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>

							<!-- Picked Tab -->
							<div class="tab-pane fade" id="picked" role="tabpanel">
								<div class="table-responsive">
									<table class="table table-hover mb-0">
										<thead class="table-light">
											<tr>
												<th>Order ID</th>
												<th>Store Name</th>
												<th>Location</th>
												<th>Amount</th>
												<th>Picked Time</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php $__empty_1 = true; $__currentLoopData = $rowsPicked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
												<tr>
													<td><a href="<?php echo e(route('delivery.panel.order.details', ['id' => $orderRow->id])); ?>" class="badge badge-success light"><?php echo e($orderCode($orderRow)); ?></a></td>
													<td><?php echo e(data_get($orderRow, 'store.store_name', 'Store N/A')); ?></td>
													<td>
														<?php echo e($storeLocation($orderRow)); ?>
														<?php $mapUrl = $storeMapUrl($orderRow); ?>
														<?php if($mapUrl): ?>
															<a href="<?php echo e($mapUrl); ?>" target="_blank" class="d-block mt-1 small text-primary">
																<i class="fas fa-map-marker-alt me-1"></i>View on Map
															</a>
														<?php endif; ?>
													</td>
													<td><strong>₹<?php echo e(number_format($fmtAmount($orderRow), 2)); ?></strong></td>
													<td><?php echo e(optional(data_get($orderRow, 'updated_at'))->format('d-m-Y h:i A')); ?></td>
													<td>
														<form method="POST" action="<?php echo e(route('delivery.panel.orders.status', ['order' => $orderRow->id])); ?>">
															<?php echo csrf_field(); ?>
															<input type="hidden" name="status" value="Out for Delivery">
															<button class="btn btn-sm btn-info" type="submit">
																<i class="fas fa-truck me-2"></i>Out for Delivery
															</button>
														</form>
													</td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
												<tr><td colspan="6" class="text-center text-muted py-4">No picked orders.</td></tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>

							<!-- Out for Delivery Tab -->
							<div class="tab-pane fade" id="outfordelivery" role="tabpanel">
								<div class="table-responsive">
									<table class="table table-hover mb-0">
										<thead class="table-light">
											<tr>
												<th>Order ID</th>
												<th>Store Name</th>
												<th>Location</th>
												<th>Amount</th>
												<th>Out Time</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php $__empty_1 = true; $__currentLoopData = $rowsOut; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
												<tr>
													<td><a href="<?php echo e(route('delivery.panel.order.details', ['id' => $orderRow->id])); ?>" class="badge badge-warning light"><?php echo e($orderCode($orderRow)); ?></a></td>
													<td><?php echo e(data_get($orderRow, 'store.store_name', 'Store N/A')); ?></td>
													<td>
														<?php echo e($storeLocation($orderRow)); ?>
														<?php $mapUrl = $storeMapUrl($orderRow); ?>
														<?php if($mapUrl): ?>
															<a href="<?php echo e($mapUrl); ?>" target="_blank" class="d-block mt-1 small text-primary">
																<i class="fas fa-map-marker-alt me-1"></i>View on Map
															</a>
														<?php endif; ?>
													</td>
													<td><strong>₹<?php echo e(number_format($fmtAmount($orderRow), 2)); ?></strong></td>
													<td><?php echo e(optional(data_get($orderRow, 'updated_at'))->format('d-m-Y h:i A')); ?></td>
													<td>
														<div class="d-flex flex-wrap" style="gap: 8px;">
															<form method="POST" action="<?php echo e(route('delivery.panel.orders.status', ['order' => $orderRow->id])); ?>">
																<?php echo csrf_field(); ?>
																<input type="hidden" name="status" value="Delivered">
																<button class="btn btn-sm btn-success" type="submit">
																	<i class="fas fa-check me-1"></i>Delivered
																</button>
															</form>
															<form method="POST" action="<?php echo e(route('delivery.panel.orders.status', ['order' => $orderRow->id])); ?>">
																<?php echo csrf_field(); ?>
																<input type="hidden" name="status" value="Failed">
																<input type="hidden" name="failure_reason" value="Delivery failed from my orders panel">
																<button class="btn btn-sm btn-danger" type="submit">
																	<i class="fas fa-times me-1"></i>Failed
																</button>
															</form>
														</div>
													</td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
												<tr><td colspan="6" class="text-center text-muted py-4">No out-for-delivery orders.</td></tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>

							<!-- Delivered Tab -->
							<div class="tab-pane fade" id="delivered" role="tabpanel">
								<div class="table-responsive">
									<table class="table table-hover mb-0">
										<thead class="table-light">
											<tr>
												<th>Order ID</th>
												<th>Store Name</th>
												<th>Location</th>
												<th>Amount</th>
												<th>Delivery Time</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											<?php $__empty_1 = true; $__currentLoopData = $rowsDelivered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
												<tr>
													<td><a href="<?php echo e(route('delivery.panel.order.details', ['id' => $orderRow->id])); ?>" class="badge badge-success light"><?php echo e($orderCode($orderRow)); ?></a></td>
													<td><?php echo e(data_get($orderRow, 'store.store_name', 'Store N/A')); ?></td>
													<td><?php echo e($storeLocation($orderRow)); ?></td>
													<td><strong>₹<?php echo e(number_format($fmtAmount($orderRow), 2)); ?></strong></td>
													<td><?php echo e(optional(data_get($orderRow, 'updated_at'))->format('d-m-Y h:i A')); ?></td>
													<td><span class="badge bg-success">Completed</span></td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
												<tr><td colspan="6" class="text-center text-muted py-4">No delivered orders.</td></tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>

							<!-- Failed / Returned Tab -->
							<div class="tab-pane fade" id="failed" role="tabpanel">
								<div class="table-responsive">
									<table class="table table-hover mb-0">
										<thead class="table-light">
											<tr>
												<th>Order ID</th>
												<th>Store Name</th>
												<th>Location</th>
												<th>Amount</th>
												<th>Reason</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											<?php $__empty_1 = true; $__currentLoopData = $rowsFailed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
												<tr>
													<td><a href="<?php echo e(route('delivery.panel.order.details', ['id' => $orderRow->id])); ?>" class="badge badge-danger light"><?php echo e($orderCode($orderRow)); ?></a></td>
													<td><?php echo e(data_get($orderRow, 'store.store_name', 'Store N/A')); ?></td>
													<td><?php echo e($storeLocation($orderRow)); ?></td>
													<td><strong>₹<?php echo e(number_format($fmtAmount($orderRow), 2)); ?></strong></td>
													<td><?php echo e((string) data_get($orderRow, 'notes', 'N/A')); ?></td>
													<td>
														<span class="badge <?php echo e((string) data_get($orderRow, 'status') === 'Returned' ? 'bg-warning' : 'bg-danger'); ?>">
															<?php echo e((string) data_get($orderRow, 'status', 'Failed')); ?>
														</span>
													</td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
												<tr><td colspan="6" class="text-center text-muted py-4">No failed/returned orders.</td></tr>
											<?php endif; ?>
										</tbody>
									</table>
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
                <p>Copyright &copy; <?php echo e($companyName ?? 'Delivery Panel'); ?> - Delivery Panel <span class="current-year">2023</span></p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->



	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->
	 <!-- Modal -->
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
	</div>
	

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="<?php echo e(asset('deliver_assets/vendor/global/global.min.js')); ?>"></script>
	<script src="<?php echo e(asset('deliver_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js')); ?>"></script>
   	<script src="<?php echo e(asset('deliver_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js')); ?>"></script>

	<script src="<?php echo e(asset('deliver_assets/js/custom.min.js')); ?>"></script>
	<script src="<?php echo e(asset('deliver_assets/js/dlabnav-init.js')); ?>"></script>
	<script src="<?php echo e(asset('deliver_assets/js/demo.js')); ?>"></script>

	<!-- localizationTool -->
	<script src="<?php echo e(asset('deliver_assets/js/jquery.localizationTool.js')); ?>"></script>
	<script src="<?php echo e(asset('deliver_assets/js/translator.js')); ?>"></script>	
	<script>
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
	</script>
	
</body>
</html>

<?php /**PATH C:\xampp\htdocs\sale_panel\admin-panel\resources\views/delivery_panel/my_orders/index.blade.php ENDPATH**/ ?>
