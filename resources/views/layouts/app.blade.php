<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'E-Commerce'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Header -->
    @include('components.header')

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-auto max-w-7xl mt-4 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-auto max-w-7xl mt-4 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if (session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 mx-auto max-w-7xl mt-4 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    @stack('scripts')
</body>
</html>
