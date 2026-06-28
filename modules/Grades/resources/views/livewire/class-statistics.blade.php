<div class="space-y-6 p-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }} - Grade Statistics</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm font-medium">Class Average</h3>
            <p class="text-4xl font-bold text-blue-600 mt-2">{{ number_format($overallClassAverage, 2) }}/20</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm font-medium">Total Subjects</h3>
            <p class="text-4xl font-bold text-green-600 mt-2">{{ $classAverages->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm font-medium">Pass Rate</h3>
            <p class="text-4xl font-bold text-purple-600 mt-2">{{ $classAverages->avg('pass_rate') ?? 0 }}%</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Highest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lowest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pass Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($classAverages as $average)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $average->subject->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $average->average }}/20</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $average->highest_score }}/20</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $average->lowest_score }}/20</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $average->pass_rate ?? 0 }}%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium">{{ $average->pass_rate ?? 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Top Students</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($studentRankings as $index => $ranking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">#{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ranking->student->first_name }} {{ $ranking->student->last_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ranking->subject->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $ranking->average }}/20</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No student data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
