<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Attendance Sessions</h2>
        <button wire:click="openCreateModal" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Create Session
        </button>
    </div>

    <div class="flex gap-4">
        <div class="flex-1">
            <input type="text" wire:model="search" placeholder="Search sessions..." class="w-full px-4 py-2 border rounded">
        </div>
        <select wire:model="perPage" class="px-4 py-2 border rounded">
            <option value="10">10 per page</option>
            <option value="25">25 per page</option>
            <option value="50">50 per page</option>
            <option value="100">100 per page</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2 text-left">Date</th>
                    <th class="border p-2 text-left">Class</th>
                    <th class="border p-2 text-left">Start Time</th>
                    <th class="border p-2 text-left">End Time</th>
                    <th class="border p-2 text-left">Attendance Rate</th>
                    <th class="border p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr class="hover:bg-gray-100">
                    <td class="border p-2">{{ $session->date->format('Y-m-d') }}</td>
                    <td class="border p-2">{{ $session->class->name ?? '-' }}</td>
                    <td class="border p-2">{{ $session->start_time ? $session->start_time->format('H:i') : '-' }}</td>
                    <td class="border p-2">{{ $session->end_time ? $session->end_time->format('H:i') : '-' }}</td>
                    <td class="border p-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">
                            {{ number_format($session->getAttendanceRate(), 1) }}%
                        </span>
                    </td>
                    <td class="border p-2 space-x-2">
                        <button class="text-blue-600 hover:underline">View</button>
                        <button class="text-red-600 hover:underline" wire:click="deleteSession({{ $session->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center">
        <div>
            Showing {{ $sessions->firstItem() ?? 0 }}-{{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }}
        </div>
        {{ $sessions->links('pagination::tailwind') }}
    </div>

    @if(session('message'))
        <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif
</div>
