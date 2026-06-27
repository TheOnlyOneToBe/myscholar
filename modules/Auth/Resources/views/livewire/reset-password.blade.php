<div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
    @if ($invalid_token)
        <div class="text-center">
            <div class="mb-6">
                <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Link Expired</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">This password reset link is invalid or has expired.</p>
            <a href="{{ route('password.request') }}" class="mt-6 inline-block text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                Request a new link
            </a>
        </div>
    @else
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Set New Password</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Enter your email and new password</p>
        </div>

        <form wire:submit="resetPassword" class="space-y-6">
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Email Address
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
                    New Password
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

            <!-- Password Confirmation Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Confirm Password
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    wire:model="password_confirmation"
                    class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="••••••••"
                >
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
            >
                Reset Password
            </button>
        </form>

        <!-- Footer Link -->
        <div class="mt-6 text-center text-gray-600 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                Back to Sign In
            </a>
        </div>
    @endif
</div>
