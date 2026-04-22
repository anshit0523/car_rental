<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body class="bg-gray-100 font-sans flex flex-col lg:flex-row min-h-screen">

    @php
        $roleName = strtolower(auth()->user()->role->name ?? '');
    @endphp

    @if($roleName === 'staff')
        @include('components.staffnavbar')
    @elseif($roleName === 'manager')
        @include('components.managernavbar')
    @else
        @include('components.admin_navbar')
    @endif

    <main class="flex-1 overflow-auto w-full lg:ml-0">
        @yield('content')
    </main>

    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')

</body>
</html>