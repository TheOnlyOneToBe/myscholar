<div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
    <div class="text-center mb-8">
        <i class="fas fa-user-plus text-blue-600 text-4xl mb-4"></i>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Create Account</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Join MyScholar today</p>
    </div>

    <form wire:submit="register" class="space-y-4">
        <!-- First Name -->
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-user text-blue-600 mr-2"></i>First Name
            </label>
            <input
                type="text"
                id="first_name"
                wire:model="first_name"
                class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="John"
            >
            @error('first_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Last Name -->
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-user text-blue-600 mr-2"></i>Last Name
            </label>
            <input
                type="text"
                id="last_name"
                wire:model="last_name"
                class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Doe"
            >
            @error('last_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
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

        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-at text-blue-600 mr-2"></i>Username
            </label>
            <input
                type="text"
                id="username"
                wire:model="username"
                class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="johndoe"
            >
            @error('username')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
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

        <!-- Password Confirmation -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-lock text-blue-600 mr-2"></i>Confirm Password
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

        <!-- Terms Agreement -->
        <div class="flex items-start">
            <input
                type="checkbox"
                id="agree_terms"
                wire:model="agree_terms"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1"
            >
            <label for="agree_terms" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                I agree to the terms and conditions
            </label>
        </div>
        @error('agree_terms')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
        >
            <i class="fas fa-check-circle mr-2"></i>Create Account
        </button>
    </form>

    <!-- Footer Link -->
    <div class="mt-6 text-center text-gray-600 dark:text-gray-400">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
            Sign in
        </a>
    </div>
</div>
