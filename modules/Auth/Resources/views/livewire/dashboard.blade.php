<div class="max-w-4xl w-full">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                Welcome, {{ $user->first_name }}!
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Here's your dashboard overview</p>
        </div>

        <!-- User Info Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Profile Info -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Full Name</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $user->first_name }} {{ $user->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Username</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $user->username }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Status</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <p class="flex items-center space-x-2">
                            <span class="inline-block w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-600' : 'bg-red-600' }}"></span>
                            <span class="text-gray-900 dark:text-white font-medium">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Joined</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Last Login</p>
                        <p class="text-gray-900 dark:text-white font-medium">
                            {{ $user->last_login ? $user->last_login->format('M d, Y H:i') : 'Never' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Roles -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Roles</h2>
            @php
                $roles = $user->currentRoles()->with('role')->get();
            @endphp
            @if ($roles->isNotEmpty())
                <div class="space-y-2">
                    @foreach ($roles as $userRole)
                        <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-3 rounded">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst(str_replace('_', ' ', $userRole->role->name)) }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $userRole->role->description }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Since {{ $userRole->started_at->format('M d, Y') }}
                                </p>
                                @if ($userRole->ended_at)
                                    <p class="text-xs text-red-600 dark:text-red-400">
                                        Expires {{ $userRole->ended_at->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400">No roles assigned yet</p>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Change Password
                </button>
                <button class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-900 dark:text-white font-medium py-2 px-4 rounded-lg transition">
                    Update Profile
                </button>
            </div>
        </div>
    </div>
</div>
