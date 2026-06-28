<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Student Dashboard - MyScholar</title>

    <!-- Styles -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        @livewire('student-sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            @livewire('student-navbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @livewire('student-dashboard-main')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
