<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">{{ $class->name ?? 'Class' }} Attendance Overview</h2>
        <div class="flex gap-2">
            <button wire:click="previousDay" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">← Previous</button>
            <button wire:click="today" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Today</button>
            <button wire:click="nextDay" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next →</button>
        </div>
    </div>

    <div class="flex gap-4">
        <div class="flex-1">
            <input type="date" wire:model="selectedDate" class="w-full px-4 py-2 border rounded">
        </div>
    </div>

    @if($overview)
    <div class="grid grid-cols-2 gap-4">
        <div class="p-4 bg-blue-100 rounded">
            <div class="text-3xl font-bold text-blue-600">{{ number_format($overview['overall_attendance_rate'], 1) }}%</div>
            <div class="text-sm text-gray-600">Class Attendance Rate</div>
        </div>
        <div class="p-4 bg-purple-100 rounded">
            <div class="text-3xl font-bold text-purple-600">{{ count($overview['sessions']) }}</div>
            <div class="text-sm text-gray-600">Sessions Today</div>
        </div>
    </div>

    @if(count($overview['sessions']) > 0)
    <div class="space-y-3">
        <h3 class="font-bold text-lg">Sessions</h3>
        @foreach($overview['sessions'] as $session)
        <div class="p-4 border rounded">
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-bold">{{ $session['time'] ?? 'All Day' }}</div>
                    <div class="text-sm text-gray-600">Session Attendance</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($session['attendance_rate'], 1) }}%</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
        No sessions scheduled for this date
    </div>
    @endif
    @else
    <div class="p-4 bg-gray-100 text-gray-600 rounded">
        Loading attendance overview...
    </div>
    @endif
</div>
