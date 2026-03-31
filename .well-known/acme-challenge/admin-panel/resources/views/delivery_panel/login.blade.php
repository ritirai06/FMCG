
<!DOCTYPE html>
<html lang="en">
<head>

	<title>FMCG Delivery Panel Login</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="shortcut icon" type="image/png" href="{{ asset('sale_assets/images/favicon.png') }}">
	<link href="{{ asset('sale_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
	<link class="main-css" href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">
	<style>
		.fix-wrapper {
			min-height: 100vh;
			display: flex;
			align-items: center;
			padding: 24px 0;
		}
		.logo-wrap {
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 8px;
			margin-bottom: 16px;
		}
		.logo-wrap img {
			max-height: 56px;
			width: auto;
			object-fit: contain;
		}
		.company-name {
			font-weight: 700;
			font-size: 24px;
			line-height: 1;
			margin: 0;
		}
		.btn-primary,
		.btn-primary:hover,
		.btn-primary:focus,
		.btn-primary:active {
			background-color: #ff4f00 !important;
			border-color: #ff4f00 !important;
			color: #fff !important;
		}
	</style>

</head>

<body>
@php
	$setting = admin_setting();
	$brandName = 'FMCG';
	$logoPath = asset('sale_assets/images/logo-full.png');
	if (!empty($setting?->profile_image)) {
		$logoPath = str_starts_with($setting->profile_image, 'http')
			? $setting->profile_image
			: asset('uploads/admin/' . ltrim($setting->profile_image, '/'));
	}
@endphp
<div class="fix-wrapper">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-5 col-md-6">
				<div class="card mb-0 h-auto">
					<div class="card-body">

						<div class="logo-wrap">
							<img class="logo-auth light" src="{{ $logoPath }}" alt="Company Logo">
							<p class="company-name">{{ $brandName }}</p>
						</div>

						<h4 class="text-center mb-2">Delivery Partner Login</h4>
						<p class="text-center text-muted mb-4">
							Login with your registered email and password
						</p>

						@if(session('success'))
							<div class="alert alert-success">{{ session('success') }}</div>
						@endif
						@if(session('error'))
							<div class="alert alert-danger">{{ session('error') }}</div>
						@endif
						@if($errors->any())
							<div class="alert alert-danger">{{ $errors->first() }}</div>
						@endif

						<form method="POST" action="{{ route('delivery.panel.login.submit') }}">
							@csrf

							<div class="form-group mb-4">
								<label class="form-label">Email Address</label>
								<input type="email"
								       class="form-control"
								       name="email"
								       value="{{ old('email') }}"
								       placeholder="Enter your email"
								       required>
							</div>

							<div class="form-group mb-4">
								<label class="form-label">Password</label>
								<input type="password"
								       class="form-control"
								       name="password"
								       placeholder="Enter your password"
								       required>
							</div>

							<div class="text-center mb-4">
								<button type="submit" class="btn btn-primary btn-block">
									Login
								</button>
							</div>

						</form>

						<div class="text-center mb-2">
							<a href="{{ route('delivery.panel.register') }}" class="text-primary">New user? Register here</a>
						</div>
						<div class="text-center">
							<a href="{{ route('delivery.panel.otp.verify.page') }}" class="text-muted">Login with OTP instead</a>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Scripts -->
<script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
</body>
</html>

