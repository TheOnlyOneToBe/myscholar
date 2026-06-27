<div class="enrollment-list-component">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-graduation-cap text-blue-600 mr-3"></i>{{ __('students.labels.enrollments') ?? 'Student Enrollments' }}
        </h2>
        <div class="flex gap-2">
            <button wire:click="exportEnrollments" class="btn btn-sm btn-primary">
                <i class="fas fa-download"></i>{{ __('common.export') ?? 'Export' }}
            </button>
            <button wire:click="resetFilters" class="btn btn-sm btn-ghost">
                <i class="fas fa-sync-alt"></i>{{ __('common.refresh') ?? 'Refresh' }}
            </button>
        </div>
    </div>

    <!-- Quick Filters -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-6">
        <button wire:click="filterToday" class="btn btn-sm btn-outline">
            <i class="fas fa-calendar-day"></i>{{ __('students.filters.today') ?? 'Today' }}
        </button>
        <button wire:click="filterThisWeek" class="btn btn-sm btn-outline">
            <i class="fas fa-calendar-week"></i>{{ __('students.filters.this_week') ?? 'This Week' }}
        </button>
        <button wire:click="filterThisMonth" class="btn btn-sm btn-outline">
            <i class="fas fa-calendar-alt"></i>{{ __('students.filters.this_month') ?? 'This Month' }}
        </button>
        <button wire:click="filterActiveOnly" class="btn btn-sm btn-outline">
            <i class="fas fa-check-circle text-green-600"></i>{{ __('students.filters.active') ?? 'Active' }}
        </button>
        <button wire:click="filterSuspendedOnly" class="btn btn-sm btn-outline">
            <i class="fas fa-pause-circle text-yellow-600"></i>{{ __('students.filters.suspended') ?? 'Suspended' }}
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-filter text-blue-600 mr-2"></i>{{ __('students.labels.filters') ?? 'Filters' }}
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('students.labels.search') ?? 'Search Student' }}
                </label>
                <input
                    type="text"
                    wire:model.live="searchQuery"
                    placeholder="{{ __('students.placeholders.search_name') ?? 'Name, ID...' }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- School Year Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('students.labels.school_year') ?? 'School Year' }}
                </label>
                <select
                    wire:model.live="filterSchoolYear"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                >
                    <option value="">{{ __('common.all') ?? 'All' }}</option>
                    @foreach($schoolYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Class Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('students.labels.class') ?? 'Class' }}
                </label>
                <input
                    type="text"
                    wire:model.live="filterClass"
                    placeholder="{{ __('students.placeholders.class') ?? 'Class ID...' }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- Filiere Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('students.labels.filiere') ?? 'Stream' }}
                </label>
                <input
                    type="text"
                    wire:model.live="filterFiliere"
                    placeholder="{{ __('students.placeholders.filiere') ?? 'Science, Littéraire...' }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('students.labels.status') ?? 'Status' }}
                </label>
                <select
                    wire:model.live="filterStatus"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                >
                    <option value="">{{ __('common.all') ?? 'All' }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}">{{ __(sprintf('students.enrollment_status.%s', $status)) ?? ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- From Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('students.labels.from_date') ?? 'From Date' }}
                </label>
                <input
                    type="date"
                    wire:model.live="filterFromDate"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                />
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button wire:click="resetFilters" class="btn btn-sm btn-secondary">
                <i class="fas fa-redo"></i>{{ __('common.reset') ?? 'Reset' }}
            </button>
        </div>
    </div>

    <!-- Enrollments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" wire:click="sortBy('student_id')">
                            <div class="flex items-center">
                                {{ __('students.labels.student') ?? 'Student' }}
                                @if($sortBy === 'student_id')
                                    <i class="fas fa-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" wire:click="sortBy('class_id')">
                            <div class="flex items-center">
                                {{ __('students.labels.class') ?? 'Class' }}
                                @if($sortBy === 'class_id')
                                    <i class="fas fa-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('students.labels.filiere') ?? 'Stream' }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('students.labels.year') ?? 'Year' }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" wire:click="sortBy('status')">
                            <div class="flex items-center">
                                {{ __('students.labels.status') ?? 'Status' }}
                                @if($sortBy === 'status')
                                    <i class="fas fa-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600" wire:click="sortBy('enrollment_date')">
                            <div class="flex items-center">
                                {{ __('students.labels.date') ?? 'Date' }}
                                @if($sortBy === 'enrollment_date')
                                    <i class="fas fa-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-2"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('common.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <div>
                                    <p class="font-medium">{{ $enrollment->student->getFullName() }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->student->student_id_number }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $enrollment->class_id ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $enrollment->filiere ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $enrollment->schoolYear?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($enrollment->status === 'active')
                                        bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                    @elseif($enrollment->status === 'suspended')
                                        bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                    @elseif($enrollment->status === 'graduated')
                                        bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                    @else
                                        bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                    @endif
                                ">
                                    <i class="fas fa-circle text-xs mr-2"></i>
                                    {{ __(sprintf('students.enrollment_status.%s', $enrollment->status)) ?? ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $enrollment->enrollment_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="viewDetail({{ $enrollment->id }})"
                                        title="{{ __('common.view') ?? 'View' }}"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @can('update', $enrollment)
                                        <button
                                            title="{{ __('common.edit') ?? 'Edit' }}"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-600"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endcan

                                    @can('manageStatus', $enrollment)
                                        @if($enrollment->status === 'active')
                                            <button
                                                wire:click="suspendEnrollment({{ $enrollment->id }})"
                                                wire:confirm="{{ __('common.confirm_action') ?? 'Are you sure?' }}"
                                                title="{{ __('common.suspend') ?? 'Suspend' }}"
                                                class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600"
                                            >
                                                <i class="fas fa-pause-circle"></i>
                                            </button>
                                        @elseif($enrollment->status === 'suspended')
                                            <button
                                                wire:click="resumeEnrollment({{ $enrollment->id }})"
                                                wire:confirm="{{ __('common.confirm_action') ?? 'Are you sure?' }}"
                                                title="{{ __('common.resume') ?? 'Resume' }}"
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-600"
                                            >
                                                <i class="fas fa-play-circle"></i>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('delete', $enrollment)
                                        <button
                                            wire:click="deleteEnrollment({{ $enrollment->id }})"
                                            wire:confirm="{{ __('common.confirm_delete') ?? 'Are you sure?' }}"
                                            title="{{ __('common.delete') ?? 'Delete' }}"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
                                {{ __('common.no_data') ?? 'No enrollments found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-300 dark:border-gray-600">
            {{ $enrollments->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetail && $this->getSelectedEnrollment())
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4">
                <div class="border-b border-gray-300 dark:border-gray-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ __('students.labels.enrollment_details') ?? 'Enrollment Details' }}
                    </h3>
                    <button wire:click="closeDetail" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-4">
                    @php $enrollment = $this->getSelectedEnrollment(); @endphp

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.student') ?? 'Student' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->student->getFullName() }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.student_id') ?? 'Student ID' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->student->student_id_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.class') ?? 'Class' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->class_id ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.filiere') ?? 'Stream' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->filiere ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.level') ?? 'Level' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->level ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.year') ?? 'Year' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->schoolYear?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.status') ?? 'Status' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ __(sprintf('students.enrollment_status.%s', $enrollment->status)) ?? ucfirst($enrollment->status) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.date') ?? 'Enrollment Date' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->enrollment_date->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    @if($enrollment->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('students.labels.notes') ?? 'Notes' }}</label>
                            <p class="text-gray-900 dark:text-white mt-1">{{ $enrollment->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-300 dark:border-gray-600 px-6 py-4 flex justify-end gap-2">
                    <button wire:click="closeDetail" class="btn btn-secondary">
                        {{ __('common.close') ?? 'Close' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .btn {
            @apply px-3 py-2 rounded-lg font-medium transition;
        }
        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
        .btn-secondary {
            @apply bg-gray-600 text-white hover:bg-gray-700;
        }
        .btn-ghost {
            @apply bg-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700;
        }
        .btn-outline {
            @apply border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700;
        }
        .btn-sm {
            @apply px-2 py-1 text-sm;
        }
    </style>
</div>
