<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $companyName ?? 'Sales Panel' }} - Dashboard</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('sale_assets/images/favicon.png') }}">
    <link href="{{ asset('sale_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/css/jquery.localizationTool.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link class="main-css" href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; }
        #main-wrapper { display: flex; min-height: 100vh; }
        .dlabnav { width: 250px; background: #13233e; color: white; overflow-y: auto; position: fixed; height: 100vh; }
        .dlabnav-scroll { padding: 20px 0; }
        .header { position: fixed; top: 0; left: 250px; right: 0; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); z-index: 100; }
        .nav-header { position: fixed; top: 0; left: 0; width: 250px; background: white; padding: 15px; border-bottom: 1px solid #eee; z-index: 101; }
        .content-body { margin-left: 250px; margin-top: 60px; padding: 20px; width: calc(100% - 250px); }
        .navbar { padding: 15px 20px; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .metismenu { list-style: none; }
        .metismenu > li { margin: 0; }
        .metismenu > li > a { display: block; padding: 12px 20px; color: #ccc; text-decoration: none; transition: all 0.3s; }
        .metismenu > li > a:hover, .metismenu > li > a.active { background: #1e3a5f; color: white; }
        .metismenu ul { list-style: none; display: none; }
        .metismenu li:hover > ul, .metismenu li.active > ul { display: block; }
        .metismenu ul li a { display: block; padding: 10px 20px 10px 40px; color: #aaa; text-decoration: none; font-size: 13px; transition: all 0.3s; }
        .metismenu ul li a:hover { background: #1e3a5f; color: white; }
        .footer { margin-left: 250px; padding: 20px; text-align: center; color: #666; border-top: 1px solid #eee; }
        .header-profile2 { padding: 15px 20px; border-bottom: 1px solid #333; }
    </style>
</head>
<body>
    <div id="preloader">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>

    <div id="main-wrapper">
        <!-- Nav header start -->
        <div class="nav-header">
            <a href="/" class="brand-logo">
                <svg class="logo-abbr" xmlns="http://www.w3.org/2000/svg" width="62.074" height="65.771" viewBox="0 0 62.074 65.771">
                    <g id="search" data-name="search" transform="translate(12.731 12.199)">
                        <rect class="rect-primary" id="Rectangle_1" data-name="Rectangle 1" width="60" height="60" rx="30" transform="translate(-10.657 -12.199)" fill="#f73a0b"></rect>
                        <path id="Path_2001" data-name="Path 2001" d="M32.7,5.18a17.687,17.687,0,0,0-25.8,24.176l-19.8,21.76a1.145,1.145,0,0,0,0,1.62,1.142,1.142,0,0,0,.81.336,1.142,1.142,0,0,0,.81-.336l19.8-21.76a17.687,17.687,0,0,0,29.357-13.29A17.57,17.57,0,0,0,32.7,5.18Zm-1.62,23.392A15.395,15.395,0,0,1,9.312,6.8,15.395,15.395,0,1,1,31.083,28.572Zm0,0" transform="translate(1 0)" fill="#fff" stroke="#fff" stroke-width="1"></path>
                    </g>
                </svg>
                <svg class="brand-title" xmlns="http://www.w3.org/2000/svg" width="134.01" height="48.365" viewBox="0 0 134.01 48.365">
                    <g id="Group_38" data-name="Group 38" transform="translate(-133.99 -40.635)">
                        <text id="SalePanel_Text" data-name="SalePanel" transform="translate(134 85)" fill="#787878" font-size="12" font-family="Poppins-Light, Poppins" font-weight="300"><tspan x="0" y="0">{{ $companyName ?? 'SalePanel' }}</tspan></text>
                    </g>
                </svg>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!-- Nav header end -->

        <!-- Header start -->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">Sales Dashboard</div>
                            <div class="nav-item d-flex align-items-center">
                                <form action="#">
                                    <div class="input-group search-area">
                                        <input type="text" class="form-control" placeholder="Search">
                                        <span class="input-group-text"><button type="submit" class="btn"><i class="flaticon-381-search-2"></i></button></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <g data-name="Layer 2" transform="translate(-2 -2)">
                                            <path id="Path_20" data-name="Path 20" d="M22.571,15.8V13.066a8.5,8.5,0,0,0-7.714-8.455V2.857a.857.857,0,0,0-1.714,0V4.611a8.5,8.5,0,0,0-7.714,8.455V15.8A4.293,4.293,0,0,0,2,20a2.574,2.574,0,0,0,2.571,2.571H9.8a4.286,4.286,0,0,0,8.4,0h5.23A2.574,2.574,0,0,0,26,20,4.293,4.293,0,0,0,22.571,15.8ZM7.143,13.066a6.789,6.789,0,0,1,6.78-6.78h.154a6.789,6.789,0,0,1,6.78,6.78v2.649H7.143ZM14,24.286a2.567,2.567,0,0,1-2.413-1.714h4.827A2.567,2.567,0,0,1,14,24.286Zm9.429-3.429H4.571A.858.858,0,0,1,3.714,20a2.574,2.574,0,0,1,2.571-2.571H21.714A2.574,2.574,0,0,1,24.286,20a.858.858,0,0,1-.857.857Z"></path>
                                        </g>
                                    </svg>
                                    <span class="badge light text-white bg-primary rounded-circle">5</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    <img src="{{ asset('sale_assets/images/profile/pic1.jpg') }}" width="20" alt="">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:void(0);" class="dropdown-item ai-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span class="ms-2">Logout</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Header end -->

        <!-- Sidebar start -->
        <div class="dlabnav">
            <div class="dlabnav-scroll">
                <div class="dropdown header-profile2">
                    <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                        <div class="header-info2 d-flex align-items-center">
                            <img src="{{ asset('sale_assets/images/profile/pic1.jpg') }}" alt="">
                            <div class="d-flex align-items-center sidebar-info">
                                <div>
                                    <span class="font-w400 d-block">{{ $salesPerson->name ?? 'Sales Person' }}</span>
                                    <small class="text-end font-w400">{{ $salesPerson->role ?? 'Sales' }}</small>
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <ul class="metismenu" id="menu">
                    <li><a class="has-arrow" href="{{ route('sale.dashboard') }}" aria-expanded="false">
                        <i class="flaticon-025-dashboard"></i>
                        <span class="nav-text">Dashboard</span>
                    </a></li>
                    <li><a class="has-arrow" href="#" aria-expanded="false">
                        <i class="flaticon-093-waving"></i>
                        <span class="nav-text">Stores</span>
                    </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('sale.store.create') }}">Create Store</a></li>
                            <li><a href="{{ route('sale.store.list') }}">Store Lists</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                        <i class="flaticon-381-user-7"></i>
                        <span class="nav-text">Orders</span>
                    </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('sale.order.create') }}">Create Order</a></li>
                            <li><a href="{{ route('sale.order.list') }}">Order History</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="copyright">
                    <p><strong>{{ $companyName ?? 'SalePanel' }}</strong> © <span class="current-year">2026</span></p>
                </div>
            </div>
        </div>
        <!-- Sidebar end -->

        <!-- Content body -->
        @yield('content')

        <!-- Footer start -->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © {{ $companyName ?? 'SalePanel' }} <span class="current-year">2026</span></p>
            </div>
        </div>
        <!-- Footer end -->
    </div>

    <!-- Required vendors -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('sale_assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('sale_assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('sale_assets/js/dlabnav-init.js') }}"></script>
    <script src="{{ asset('sale_assets/js/demo.js') }}"></script>
    <script src="{{ asset('sale_assets/js/styleSwitcher.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.metismenu > li > a').click(function(e) {
                $(this).parent().toggleClass('active');
            });
        });
    </script>
</body>
</html>
