<!DOCTYPE html>
<html lang="en">
<head>

	<title>Delivery Panel Register</title>

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

		/* freshly added selectors to hide injected promo/support/buy now */
		#tatvoap, /* yellow popup */
		#support-widget, /* generic support widget */
		#buy-now, /* generic buy now buttons */
		.aioxd, /* earlier observed promo classes */
		.fp-model-close, /* close button proxies */
		footer .subscribe-box { display: none !important; }
	</style>

</head>

<body>
	@php
		$setting = admin_setting();
		$brandName = 'Delivery Panel';
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
									<div class="input-group">
									  <input type="password" id="password" class="form-control" name="password" placeholder="Enter password" required>
									  <button type="button" class="btn btn-outline-secondary pw-toggle" onclick="dpRegToggle('password','dreg_eye1')"><i class="bi bi-eye" id="dreg_eye1"></i></button>
									</div>
								</div>

								<div class="mb-sm-4 mb-3">
									<label class="form-label" for="password_confirmation">Confirm Password</label>
									<div class="input-group">
									  <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="Confirm password" required>
									  <button type="button" class="btn btn-outline-secondary pw-toggle" onclick="dpRegToggle('password_confirmation','dreg_eye2')"><i class="bi bi-eye" id="dreg_eye2"></i></button>
									</div>
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

	<script>
	function dpRegToggle(id, iconId) {
	  const input = document.getElementById(id);
	  const icon  = document.getElementById(iconId);
	  input.type  = input.type === 'password' ? 'text' : 'password';
	  icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
	}
	// Remove all promo modals/popups/overlays, support and buy now floating widgets from delivery panel register page
	(function() {
		function removeDeliveryPromos() {
			// Remove by explicit selectors
			['#tatvoap', '#support-widget', '#buy-now', '.aioxd', '.fp-model-close', '.support', '.buy-now', '.supportbtn', '.buy-now-btn', '.modal-backdrop', '.rbl-backdrop', '.whatsapp-support', '.whatsapp-support-btn', '.floating-support', '.floating-cart', '.float-support', '.float-cart', '.float-btn', '.float-button', '.float-buynow', '.float-support-btn', '.float-buynow-btn'].forEach(sel => {
				document.querySelectorAll(sel).forEach(el => el.remove());
			});
			// Remove any modal/popup/backdrop with promo/support/buy text
			document.querySelectorAll('div,section,a,button').forEach(el => {
				const txt = (el.innerText||'').toLowerCase();
				if(/month\s*end\s*sale|grab\s*sale|special\s*offer|promo|envato|buy\s*now|support|cart|help|whatsapp/i.test(txt)) {
					// Only remove if it's floating/fixed or at bottom left
					const cs = window.getComputedStyle(el);
					if(cs.position === 'fixed' || cs.position === 'sticky' || (parseInt(cs.bottom) >= 0 && parseInt(cs.left) >= 0)) {
						el.remove();
					}
				}
			});
			// Remove overlays with suspicious style
			document.querySelectorAll('div,section').forEach(el => {
				const bg = window.getComputedStyle(el).backgroundColor;
				if(bg && (bg.includes('rgba') || bg.includes('rgb')) && el.offsetWidth > 300 && el.offsetHeight > 200 && el !== document.body && el !== document.querySelector('.fix-wrapper')) {
					// If high z-index and covers most of viewport, remove
					const z = parseInt(window.getComputedStyle(el).zIndex) || 0;
					if(z > 10 && el.offsetWidth > window.innerWidth*0.5 && el.offsetHeight > window.innerHeight*0.5) {
						el.remove();
					}
				}
			});
		}
		removeDeliveryPromos();
		new MutationObserver(removeDeliveryPromos).observe(document.body, {childList:true, subtree:true});
		// Stop observer after 30s
		setTimeout(()=>{removeDeliveryPromos();}, 30000);
	})();
	</script>
	<style>
	/* Hide common floating support/buy now/cart widgets visually as fallback */
	.whatsapp-support, .whatsapp-support-btn, .floating-support, .floating-cart, .float-support, .float-cart, .float-btn, .float-button, .float-buynow, .float-support-btn, .float-buynow-btn {
	  display: none !important;
	  visibility: hidden !important;
	  pointer-events: none !important;
	}
	</style>
</body>
</html>
