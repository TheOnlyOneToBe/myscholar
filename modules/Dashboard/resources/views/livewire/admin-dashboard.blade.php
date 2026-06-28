<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back! Here's your school's performance at a glance.</p>
            </div>
            <button wire:click="refresh" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Students -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Students</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $quickStats['total_students'] ?? 0 }}</p>
                    </div>
                    <svg class="w-12 h-12 text-blue-100" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 12a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>
            </div>

            <!-- Active Classes -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Classes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $quickStats['active_classes'] ?? 0 }}</p>
                    </div>
                    <svg class="w-12 h-12 text-green-100" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                    </svg>
                </div>
            </div>

            <!-- Grade Appeals Pending -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Appeals Pending</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingAppeals ?? 0 }}</p>
                    </div>
                    <svg class="w-12 h-12 text-yellow-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18.868 1.132a1 1 0 011.004 1.005l-.707 7.07a1 1 0 01-.997.929h-.003l-7.071-.707a1 1 0 11.14-1.414l4.95.494L11.81 5.05a5 5 0 10-7.07 7.07l-1.414-1.414a7 7 0 1110.142-9.474l.707-7.071z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <!-- IP Blocks Active -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">IP Blocks Active</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $quickStats['ip_blocks_active'] ?? 0 }}</p>
                    </div>
                    <svg class="w-12 h-12 text-red-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.526a6 6 0 008.367 8.368zM17.618 4.374a.75.75 0 00-1.06-1.06l-13.25 13.25a.75.75 0 101.06 1.06l13.25-13.25z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <!-- Attendance Rate -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Attendance Rate</h3>
                    <span class="text-3xl font-bold text-green-600">{{ $attendanceRate }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $attendanceRate }}%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">Overall school attendance</p>
            </div>

            <!-- Average Grade -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Average Grade</h3>
                    <span class="text-3xl font-bold text-blue-600">{{ $averageGrade }}</span>
                </div>
                <div class="text-sm text-gray-600">
                    <p>School average score</p>
                    <p class="text-xs mt-1">Based on all grades</p>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Health (24h)</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">API Requests:</span>
                        <span class="font-medium">{{ $systemHealth['total_api_requests_24h'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Failed:</span>
                        <span class="font-medium text-red-600">{{ $systemHealth['failed_requests_24h'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Errors:</span>
                        <span class="font-medium text-red-600">{{ $systemHealth['critical_errors_24h'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 2a1 1 0 000 2h14a1 1 0 100-2H3z" clip-rule="evenodd" />
                    </svg>
                    Recent Activity
                </h2>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($recentActivity as $activity)
                        <div class="border-l-4 pl-3 py-2" :class="[
                            'border-green-500' => $activity['severity'] === 'info',
                            'border-yellow-500' => $activity['severity'] === 'warning',
                            'border-red-500' => $activity['severity'] === 'error' || $activity['severity'] === 'critical',
                        ]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ str_replace('_', ' ', ucfirst($activity['action'])) }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">by <strong>{{ $activity['user_name'] }}</strong></p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $activity['created_at'] }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4 text-center">No recent activity</p>
                    @endforelse
                </div>
            </div>

            <!-- Subject Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                    Subject Averages
                </h2>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($subjectAverages as $subject)
                        <div class="pb-3 border-b last:border-b-0">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ $subject['subject'] }}</span>
                                <span class="text-sm font-bold text-blue-600">{{ $subject['average'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min($subject['average'], 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $subject['total_grades'] }} grades</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4 text-center">No grade data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Performance Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Top Absent Students -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.218c0 1.257-.34 2.477-.972 3.515a5.422 5.422 0 01-2.65 2.237 5.5 5.5 0 01-5.617-1.409.5.5 0 10-.707.707A6.5 6.5 0 0010 18.5a6.5 6.5 0 003.832-1.355c1.12-.913 1.954-2.157 2.542-3.57.531-1.204.806-2.54.806-3.96V6.517a2.066 2.066 0 00-1.895-2.062 2.066 2.066 0 00-1.175.484 2.066 2.066 0 01-2.662 0 2.066 2.066 0 00-1.175-.484 2.066 2.066 0 00-1.895 2.062v6.218zm6 5.22a.75.75 0 00-1.06-1.061L9 9.94l-1.146-1.147a.75.75 0 10-1.06 1.06l1.147 1.147-1.147 1.146a.75.75 0 10 1.06 1.06l1.147-1.146 1.146 1.147a.75.75 0 101.06-1.06l-1.147-1.147 1.147-1.146z" clip-rule="evenodd" />
                    </svg>
                    Top Absent
                </h2>
                <div class="space-y-3">
                    @forelse($topAbsentStudents as $student)
                        <div class="pb-3 border-b last:border-b-0">
                            <p class="font-medium text-sm text-gray-900">{{ $student['name'] }}</p>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-gray-600">{{ $student['absences'] }} absences</span>
                                <span class="text-xs font-bold text-red-600">{{ $student['absence_rate'] }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4 text-center">No absence data</p>
                    @endforelse
                </div>
            </div>

            <!-- Highest Performers -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Top Performers
                </h2>
                <div class="space-y-3">
                    @forelse($highestPerformers as $student)
                        <div class="pb-3 border-b last:border-b-0">
                            <p class="font-medium text-sm text-gray-900">{{ $student['name'] }}</p>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-gray-600">Average</span>
                                <span class="text-xs font-bold text-green-600">{{ $student['average'] }} ({{ $student['grade'] }})</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4 text-center">No grade data</p>
                    @endforelse
                </div>
            </div>

            <!-- Low Performers (Need Support) -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Need Support
                </h2>
                <div class="space-y-3">
                    @forelse($lowPerformers as $student)
                        <div class="pb-3 border-b last:border-b-0">
                            <p class="font-medium text-sm text-gray-900">{{ $student['name'] }}</p>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-gray-600">Average</span>
                                <span class="text-xs font-bold text-yellow-600">{{ $student['average'] }} ({{ $student['grade'] }})</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm py-4 text-center">All students passing</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
