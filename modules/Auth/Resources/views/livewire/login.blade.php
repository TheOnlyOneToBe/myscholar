<div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
    <div class="text-center mb-8">
        <i class="fas fa-sign-in-alt text-blue-600 text-4xl mb-4"></i>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Sign In</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Welcome back to MyScholar</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
            <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <form wire:submit="login" class="space-y-6">
        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-envelope text-blue-600 mr-2"></i>Email Address
            </label>
            <input
                type="email"
                id="email"
                wire:model="email"
                class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="you@example.com"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-lock text-blue-600 mr-2"></i>Password
            </label>
            <input
                type="password"
                id="password"
                wire:model="password"
                class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                type="checkbox"
                id="remember"
                wire:model="remember"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                Remember me
            </label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
        >
            <i class="fas fa-arrow-right-to-bracket mr-2"></i>Sign In
        </button>
    </form>

    <!-- Footer Links -->
    <div class="mt-6 space-y-4">
        <div class="text-center">
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                Forgot your password?
            </a>
        </div>
        <div class="text-center text-gray-600 dark:text-gray-400">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                Sign up
            </a>
        </div>
    </div>
</div>
