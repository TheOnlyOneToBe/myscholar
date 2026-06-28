<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - MyScholar</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h2 class="text-xl font-bold text-gray-900">MyScholar</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <a href="{{ route('logout') }}" class="text-sm text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        @livewire('dashboard::admin-dashboard')
    </div>

    @livewireScripts
</body>
</html>
