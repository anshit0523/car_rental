<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Car Rental - User Dashboard')</title>

    <link href="{{ asset('template/assets/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/fontawesome-all.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/iconfont.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/owl.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/global.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/jquery.fancybox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/header.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/footer.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/booking-form.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/style.css') }}" rel="stylesheet">
    <link href="{{ asset('template/assets/responsive.css') }}" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <style>
        body {
            background: #f7f7f7;
        }

        main {
            padding-top: 30px;
        }

        @yield('custom-styles')
    </style>
</head>
<body class="boxed_wrapper">

    @if (session('success'))
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 99999;" role="alert">
            <div style="background:#d1fae5; border:1px solid #10b981; color:#065f46; padding:16px 22px; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,.12); min-width:300px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 99999;" role="alert">
            <div style="background:#fee2e2; border:1px solid #ef4444; color:#991b1b; padding:16px 22px; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,.12); min-width:300px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-times-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    @include('components.user_navbar')

    <main>
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('template/assets/js/bootstrap.bundle.min.js') }}"></script>
 
    <script src="{{ asset('js/user/usernavbar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('[role="alert"]');

            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-15px)';
                    alert.style.transition = 'all 0.3s ease';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        }
    </script>

    @yield('scripts')
</body>
</html>