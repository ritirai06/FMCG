<!DOCTYPE html>
<html lang="en">
<head>

	<title>FMCG Delivery Panel Register</title>

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
		.new-account,
		.new-account p {
			text-align: center;
			margin-bottom: 0;
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

							<h4 class="text-center mb-4">Sign up your account</h4>

							@if(session('success'))
								<div class="alert alert-success">{{ session('success') }}</div>
							@endif
							@if(session('error'))
								<div class="alert alert-danger">{{ session('error') }}</div>
							@endif
							@if($errors->any())
								<div class="alert alert-danger">{{ $errors->first() }}</div>
							@endif

							<form method="POST" action="{{ route('delivery.panel.register.submit') }}">
								@csrf

								<div class="form-group mb-4">
									<label class="form-label" for="name">Username</label>
									<input type="text" class="form-control" placeholder="Enter username" id="name" name="name" value="{{ old('name') }}" required>
								</div>

								<div class="form-group mb-4">
									<label class="form-label" for="email">Email</label>
									<input type="email" class="form-control" placeholder="hello@example.com" id="email" name="email" value="{{ old('email') }}" required>
								</div>

								<div class="form-group mb-4">
									<label class="form-label" for="phone">Mobile Number</label>
									<input type="text" class="form-control" placeholder="Enter 10 digit mobile number" id="phone" name="phone" value="{{ old('phone') }}" maxlength="10" pattern="[0-9]{10}" required>
								</div>

								<div class="mb-sm-4 mb-3">
									<label class="form-label" for="password">Password</label>
									<input type="password" id="password" class="form-control" name="password" placeholder="Enter password" required>
								</div>

								<div class="mb-sm-4 mb-3">
									<label class="form-label" for="password_confirmation">Confirm Password</label>
									<input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="Confirm password" required>
								</div>

								<div class="text-center">
									<button type="submit" class="btn btn-primary btn-block">Sign up</button>
								</div>
							</form>

							<div class="new-account mt-3">
								<p>Already have an account? <a class="text-primary" href="{{ route('delivery.panel.login') }}">Sign in</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
	<script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
</body>
</html>
