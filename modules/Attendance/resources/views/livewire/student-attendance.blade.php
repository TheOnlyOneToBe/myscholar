<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">{{ $student->first_name ?? 'Student' }} Attendance</h2>
        <button wire:click="openMarkModal" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Mark Attendance
        </button>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="p-4 bg-blue-100 rounded">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($attendanceRate, 1) }}%</div>
            <div class="text-sm text-gray-600">Attendance Rate</div>
        </div>
        <div class="p-4 {{ $isPassingRate ? 'bg-green-100' : 'bg-red-100' }} rounded">
            <div class="text-lg font-bold {{ $isPassingRate ? 'text-green-600' : 'text-red-600' }}">
                {{ $isPassingRate ? 'PASSING' : 'FAILING' }}
            </div>
            <div class="text-sm text-gray-600">Status</div>
        </div>
        <div class="p-4 bg-gray-100 rounded">
            <div class="text-lg font-bold text-gray-600">{{ $records->total() }}</div>
            <div class="text-sm text-gray-600">Total Records</div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2 text-left">Date</th>
                    <th class="border p-2 text-left">Session</th>
                    <th class="border p-2 text-left">Status</th>
                    <th class="border p-2 text-left">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr class="hover:bg-gray-100">
                    <td class="border p-2">{{ $record->session->date->format('Y-m-d') }}</td>
                    <td class="border p-2">{{ $record->session->subject->name ?? 'General' }}</td>
                    <td class="border p-2">
                        <span class="px-2 py-1 rounded text-white
                            {{ $record->status === 'present' ? 'bg-green-500' : '' }}
                            {{ $record->status === 'absent' ? 'bg-red-500' : '' }}
                            {{ $record->status === 'late' ? 'bg-yellow-500' : '' }}
                            {{ $record->status === 'excused' ? 'bg-blue-500' : '' }}
                        ">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                    <td class="border p-2">{{ $record->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center">
        <div>
            Showing {{ $records->firstItem() ?? 0 }}-{{ $records->lastItem() ?? 0 }} of {{ $records->total() }}
        </div>
        {{ $records->links('pagination::tailwind') }}
    </div>

    @if(session('message'))
        <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif
</div>
