<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body class="bg-gray-100 font-sans flex flex-col lg:flex-row min-h-screen">

    {{-- Admin Navbar --}}
    @include('components.admin_navbar')

    {{-- Main Content --}}
    <main class="flex-1 overflow-auto w-full lg:ml-0">
        @yield('content')
    </main>

    {{-- Scripts --}}
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')

</body>
</html>
