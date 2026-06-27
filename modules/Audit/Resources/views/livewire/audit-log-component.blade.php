<div class="audit-logs-container">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-history text-blue-600 mr-3"></i>{{ __('audit.labels.audit_logs') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('audit.messages.logs_retrieved') }}</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="exportLogs" class="btn btn-sm btn-primary flex items-center gap-2">
                    <i class="fas fa-download"></i>{{ __('common.export') ?? 'Export' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-2">
            <button wire:click="filterToday" class="btn btn-xs btn-outline">
                <i class="fas fa-calendar-day"></i>{{ __('audit.filters.today') }}
            </button>
            <button wire:click="filterThisWeek" class="btn btn-xs btn-outline">
                <i class="fas fa-calendar-week"></i>{{ __('audit.filters.this_week') }}
            </button>
            <button wire:click="filterThisMonth" class="btn btn-xs btn-outline">
                <i class="fas fa-calendar-alt"></i>{{ __('audit.filters.this_month') }}
            </button>
            <div class="divider divider-horizontal"></div>
            <button wire:click="filterErrorsOnly" class="btn btn-xs btn-outline btn-warning">
                <i class="fas fa-exclamation-triangle"></i>{{ __('audit.filters.errors_only') }}
            </button>
            <button wire:click="filterCriticalOnly" class="btn btn-xs btn-outline btn-error">
                <i class="fas fa-exclamation-circle"></i>{{ __('audit.severity_levels.critical') }}
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label class="label">
                    <span class="label-text">{{ __('common.search') ?? 'Search' }}</span>
                </label>
                <input
                    type="text"
                    wire:model.live="searchQuery"
                    placeholder="{{ __('common.search') }}"
                    class="input input-bordered input-sm w-full dark:bg-gray-700"
                >
            </div>

            <!-- Action Filter -->
            <div>
                <label class="label">
                    <span class="label-text">{{ __('audit.filters.by_action') }}</span>
                </label>
                <select wire:model.live="filterAction" class="select select-bordered select-sm w-full dark:bg-gray-700">
                    <option value="">{{ __('common.all') ?? 'All' }}</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}">{{ __('audit.actions.' . $action) ?? ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- User Filter -->
            <div>
                <label class="label">
                    <span class="label-text">{{ __('audit.filters.by_user') }}</span>
                </label>
                <select wire:model.live="filterUser" class="select select-bordered select-sm w-full dark:bg-gray-700">
                    <option value="">{{ __('common.all') ?? 'All' }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Severity Filter -->
            <div>
                <label class="label">
                    <span class="label-text">{{ __('audit.filters.by_severity') }}</span>
                </label>
                <select wire:model.live="filterSeverity" class="select select-bordered select-sm w-full dark:bg-gray-700">
                    <option value="">{{ __('common.all') ?? 'All' }}</option>
                    @foreach ($severities as $severity)
                        <option value="{{ $severity }}">{{ __('audit.severity_levels.' . $severity) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Entity Type Filter -->
            <div>
                <label class="label">
                    <span class="label-text">{{ __('audit.filters.by_entity_type') }}</span>
                </label>
                <select wire:model.live="filterEntityType" class="select select-bordered select-sm w-full dark:bg-gray-700">
                    <option value="">{{ __('common.all') ?? 'All' }}</option>
                    @foreach ($entityTypes as $type)
                        <option value="{{ $type }}">{{ __('audit.entity_types.' . $type) ?? ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="lg:col-span-2">
                <label class="label">
                    <span class="label-text">{{ __('audit.filters.by_date_range') }}</span>
                </label>
                <div class="flex gap-2">
                    <input
                        type="date"
                        wire:model.live="filterFromDate"
                        class="input input-bordered input-sm flex-1 dark:bg-gray-700"
                    >
                    <input
                        type="date"
                        wire:model.live="filterToDate"
                        class="input input-bordered input-sm flex-1 dark:bg-gray-700"
                    >
                </div>
            </div>
        </div>

        <!-- Reset Filters -->
        <div class="mt-4">
            <button wire:click="resetFilters" class="btn btn-sm btn-ghost">
                <i class="fas fa-times"></i>{{ __('common.reset') ?? 'Reset Filters' }}
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" wire:click="sortBy('created_at')">
                            <i class="fas fa-arrow-down text-gray-400 mr-2"></i>{{ __('audit.labels.timestamp') }}
                        </th>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" wire:click="sortBy('action')">
                            {{ __('audit.labels.action') }}
                        </th>
                        <th class="px-4 py-3 text-left">{{ __('audit.labels.user') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('audit.labels.entity_type') }}</th>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" wire:click="sortBy('severity')">
                            {{ __('audit.labels.severity') }}
                        </th>
                        <th class="px-4 py-3 text-left">{{ __('audit.labels.ip_address') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('common.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $log->isError() ? 'bg-red-50 dark:bg-red-950' : '' }}">
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge badge-outline">
                                    <i class="fas fa-circle text-xs mr-1"></i>{{ __('audit.actions.' . $log->action) ?? ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($log->user)
                                    <span class="text-sm">{{ $log->user->first_name }} {{ $log->user->last_name }}</span>
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $log->user->email }}</span>
                                @else
                                    <span class="text-gray-400 italic">{{ __('common.system') ?? 'System' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge">
                                    {{ __('audit.entity_types.' . $log->entity_type) ?? $log->entity_type }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge @if($log->severity === 'critical') badge-error @elseif($log->severity === 'error') badge-warning @elseif($log->severity === 'warning') badge-info @else badge-success @endif">
                                    {{ __('audit.severity_levels.' . $log->severity) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-gray-600 dark:text-gray-400">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button
                                        wire:click="viewDetail({{ $log->id }})"
                                        class="btn btn-xs btn-ghost"
                                        title="{{ __('common.view_details') ?? 'View Details' }}"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if (auth()->user()?->can('audit.delete'))
                                        <button
                                            wire:click="deleteLog({{ $log->id }})"
                                            wire:confirm="{{ __('common.confirm_delete') ?? 'Are you sure?' }}"
                                            class="btn btn-xs btn-ghost btn-error"
                                            title="{{ __('common.delete') }}"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>{{ __('common.no_data') ?? 'No data found' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if ($showDetail && $this->getSelectedLog())
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeDetail">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto" wire:click.stop>
                @php $log = $this->getSelectedLog() @endphp

                <div class="sticky top-0 bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <h2 class="text-xl font-bold">{{ __('audit.labels.log_detail') }}</h2>
                    <button wire:click="closeDetail" class="btn btn-ghost btn-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ __('audit.labels.timestamp') }}</h3>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ __('audit.labels.action') }}</h3>
                            <p class="text-sm text-gray-900 dark:text-white">{{ __('audit.actions.' . $log->action) ?? ucfirst($log->action) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ __('audit.labels.user') }}</h3>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $log->user?->first_name }} {{ $log->user?->last_name }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ __('audit.labels.entity_type') }}</h3>
                            <p class="text-sm text-gray-900 dark:text-white">{{ __('audit.entity_types.' . $log->entity_type) ?? $log->entity_type }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ __('audit.labels.severity') }}</h3>
                            <p class="text-sm text-gray-900 dark:text-white">{{ __('audit.severity_levels.' . $log->severity) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ __('audit.labels.ip_address') }}</h3>
                            <p class="text-sm text-gray-900 dark:text-white font-mono">{{ $log->ip_address }}</p>
                        </div>
                    </div>

                    <!-- Request Details -->
                    @if ($log->url)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('common.request') ?? 'Request Details' }}</h3>
                            <div class="space-y-2 text-sm">
                                <div><span class="text-gray-600 dark:text-gray-400">{{ __('audit.labels.method') }}:</span> <span class="font-mono text-blue-600">{{ $log->method }}</span></div>
                                <div><span class="text-gray-600 dark:text-gray-400">{{ __('audit.labels.url') }}:</span> <span class="font-mono text-blue-600 break-all">{{ $log->url }}</span></div>
                                @if ($log->http_status)
                                    <div><span class="text-gray-600 dark:text-gray-400">{{ __('audit.labels.status_code') }}:</span> <span class="font-mono">{{ $log->http_status }}</span></div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Changes -->
                    @if ($log->getChangedFields())
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('audit.labels.changes') }}</h3>
                            <div class="space-y-2 text-sm">
                                @foreach ($log->getChangedFields() as $field => $change)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $field }}</p>
                                        <p class="text-red-600"><strong>{{ __('audit.labels.old_values') }}:</strong> {{ $change['old'] ?? 'N/A' }}</p>
                                        <p class="text-green-600"><strong>{{ __('audit.labels.new_values') }}:</strong> {{ $change['new'] ?? 'N/A' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Error -->
                    @if ($log->error_message)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('audit.labels.error_message') }}</h3>
                            <div class="bg-red-50 dark:bg-red-950 p-3 rounded text-sm font-mono text-red-800 dark:text-red-200 break-all">
                                {{ $log->error_message }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .audit-logs-container .btn {
        @apply px-3 py-2 rounded-lg font-medium transition;
    }

    .audit-logs-container .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600;
    }

    .audit-logs-container .btn-outline {
        @apply border border-gray-300 dark:border-gray-600 bg-transparent text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700;
    }

    .audit-logs-container .btn-ghost {
        @apply bg-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700;
    }

    .audit-logs-container .btn-xs {
        @apply px-2 py-1 text-xs;
    }

    .audit-logs-container .btn-sm {
        @apply px-2 py-1 text-sm;
    }

    .audit-logs-container .badge {
        @apply inline-block px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200;
    }

    .audit-logs-container .badge-outline {
        @apply border border-gray-300 dark:border-gray-600;
    }

    .audit-logs-container .badge-error {
        @apply bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200;
    }

    .audit-logs-container .badge-warning {
        @apply bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200;
    }

    .audit-logs-container .badge-info {
        @apply bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200;
    }

    .audit-logs-container .badge-success {
        @apply bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200;
    }

    .audit-logs-container .divider {
        @apply mx-2;
    }

    .audit-logs-container .select {
        @apply px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white;
    }

    .audit-logs-container .input {
        @apply px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white;
    }
</style>
