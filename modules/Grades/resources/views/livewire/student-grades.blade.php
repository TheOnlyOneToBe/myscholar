<div class="space-y-6 p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}'s Grades</h1>
    </div>

    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
        <div class="text-center">
            <p class="text-blue-100 text-sm">Overall Average</p>
            <p class="text-5xl font-bold">{{ number_format($overallAverage, 2) }}/20</p>
            <p class="text-blue-100 mt-2">{{ $overallAverage >= 10 ? 'Passed' : 'Failed' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($averages as $average)
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-bold text-gray-900">{{ $average->subject->name }}</h3>
                <p class="text-3xl font-bold mt-2">{{ number_format($average->average, 2) }}/20</p>
                <p class="text-sm text-gray-500 mt-1">
                    @if($average->is_passed)
                        <span class="text-green-600">✓ Passed</span>
                    @else
                        <span class="text-red-600">✗ Failed</span>
                    @endif
                </p>
                @if($average->rank)
                    <p class="text-sm text-gray-500">Rank: {{ $average->rank }}</p>
                @endif
            </div>
        @empty
            <p class="text-gray-500">No grades available</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($grades as $grade)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $grade->subject->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">{{ $grade->score }}/20</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($grade->grade_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $grade->gradePeriod->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $grade->teacher->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No grades found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
