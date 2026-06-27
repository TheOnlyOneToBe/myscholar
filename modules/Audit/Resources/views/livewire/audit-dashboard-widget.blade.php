<div class="audit-dashboard-widget">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-chart-line text-blue-600 mr-3"></i>{{ __('audit.labels.statistics') }}
        </h2>
        <button wire:click="refresh" class="btn btn-sm btn-ghost">
            <i class="fas fa-sync-alt"></i>{{ __('common.refresh') ?? 'Refresh' }}
        </button>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('audit.dashboard.total_logs') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($totalLogs) }}</p>
                </div>
                <div class="text-4xl text-blue-100 dark:text-blue-900">
                    <i class="fas fa-list"></i>
                </div>
            </div>
        </div>

        <!-- Today's Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('audit.dashboard.today_logs') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $todayLogs }}</p>
                </div>
                <div class="text-4xl text-green-100 dark:text-green-900">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <!-- Recent Errors -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('audit.dashboard.recent_errors') }}</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $recentErrors }}</p>
                </div>
                <div class="text-4xl text-red-100 dark:text-red-900">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <!-- Critical Errors -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('audit.severity_levels.critical') }}</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-2">{{ $criticalErrors }}</p>
                </div>
                <div class="text-4xl text-orange-100 dark:text-orange-900">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
        </div>

        <!-- Failed Requests -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('audit.dashboard.failed_requests') }}</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $failedRequests }}</p>
                </div>
                <div class="text-4xl text-red-100 dark:text-red-900">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

        <!-- Error Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('audit.dashboard.error_rate') }}</p>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $errorRate }}%</p>
                </div>
                <div class="text-4xl text-yellow-100 dark:text-yellow-900">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Errors -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-bug text-red-600 mr-2"></i>{{ __('audit.dashboard.top_errors') }}
            </h3>

            @if (count($topErrors) > 0)
                <div class="space-y-3">
                    @foreach ($topErrors as $error)
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm text-gray-700 dark:text-gray-300 break-words">{{ $error['message'] }}</p>
                            </div>
                            <span class="ml-2 inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                {{ $error['count'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('common.no_data') ?? 'No data' }}</p>
            @endif
        </div>

        <!-- Most Active Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-users text-blue-600 mr-2"></i>{{ __('audit.dashboard.most_active_users') }}
            </h3>

            @if (count($mostActiveUsers) > 0)
                <div class="space-y-3">
                    @foreach ($mostActiveUsers as $index => $user)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-bold mr-3">
                                    {{ $index + 1 }}
                                </span>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $user['user'] }}</p>
                            </div>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                {{ $user['count'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('common.no_data') ?? 'No data' }}</p>
            @endif
        </div>

        <!-- Most Accessed Routes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-road text-green-600 mr-2"></i>{{ __('audit.dashboard.most_accessed_routes') }}
            </h3>

            @if (count($mostAccessedRoutes) > 0)
                <div class="space-y-3">
                    @foreach ($mostAccessedRoutes as $route)
                        <div class="flex items-start justify-between">
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-mono break-words">{{ $route['url'] }}</p>
                            <span class="ml-2 inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 whitespace-nowrap">
                                {{ $route['count'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('common.no_data') ?? 'No data' }}</p>
            @endif
        </div>
    </div>
</div>

<style>
    .audit-dashboard-widget .btn {
        @apply px-3 py-2 rounded-lg font-medium transition;
    }

    .audit-dashboard-widget .btn-ghost {
        @apply bg-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700;
    }

    .audit-dashboard-widget .btn-sm {
        @apply px-2 py-1 text-sm;
    }
</style>
