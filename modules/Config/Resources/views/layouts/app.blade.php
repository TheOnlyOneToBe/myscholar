<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyScholar') }} - Configuration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3H7bCOEP8E4CsCS5pQBqwXfugggAa+ks/0VYi+3x6z7+axAL3+IBrMHS/8R5TdRr5kjjS0ohEGA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-blue-600">MyScholar</a>
                <div class="flex gap-4">
                    @auth
                        <span class="text-gray-700 dark:text-gray-300">{{ Auth::user()->first_name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-12">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <livewire:config.footer-component />

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
