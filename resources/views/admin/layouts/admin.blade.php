<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'E-Commerce') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-gray-800">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 bg-gray-900 px-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-white text-xl font-bold">
                        Admin Panel
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto px-2 py-4">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Products -->
                    <div class="mt-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Products
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('admin.products.*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            All Products
                        </a>
                        <a href="{{ route('admin.products.create') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New Product
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Categories
                        </a>
                    </div>

                    <!-- Orders -->
                    <div class="mt-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Orders
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            All Orders
                        </a>
                    </div>

                    <!-- Transactions -->
                    <div class="mt-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Payments
                        </div>
                        <a href="{{ route('admin.transactions.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('admin.transactions.*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Transactions
                        </a>
                    </div>

                    <!-- Settings -->
                    <div class="mt-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Settings
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Site Settings
                        </a>
                    </div>
                </nav>

                <!-- User Info & Logout -->
                <div class="flex-shrink-0 flex border-t border-gray-700 p-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-white">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h1>

                    <div class="flex items-center space-x-4">
                        <!-- Back to Site -->
                        <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            View Site
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('info') }}</span>
                            </div>
                        @endif

                        <!-- Main Content Area -->
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
