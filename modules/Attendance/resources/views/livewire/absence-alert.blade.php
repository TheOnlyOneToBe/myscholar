<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Absence Alerts</h2>
        <button wire:click="togglePendingFilter" class="px-4 py-2 {{ $showPendingOnly ? 'bg-blue-500 text-white' : 'bg-gray-300' }} rounded hover:bg-blue-600">
            {{ $showPendingOnly ? 'Show All' : 'Show Pending Only' }}
        </button>
    </div>

    <select wire:model="perPage" class="px-4 py-2 border rounded">
        <option value="10">10 per page</option>
        <option value="25">25 per page</option>
        <option value="50">50 per page</option>
    </select>

    <div class="space-y-3">
        @forelse($alerts as $alert)
        <div class="p-4 border rounded shadow-sm {{ $alert->is_acknowledged ? 'bg-gray-50 opacity-75' : 'bg-white' }}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-bold text-lg">{{ $alert->student->first_name ?? 'Student' }} {{ $alert->student->last_name ?? '' }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ $alert->reason }}</div>
                    @if($alert->absence_threshold)
                    <div class="text-xs text-gray-500 mt-1">Threshold: {{ $alert->absence_threshold }} days</div>
                    @endif
                    <div class="text-xs text-gray-500 mt-2">{{ $alert->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div class="flex flex-col gap-2 items-end">
                    <span class="px-3 py-1 rounded text-white {{ $alert->is_acknowledged ? 'bg-green-500' : 'bg-yellow-500' }}">
                        {{ $alert->is_acknowledged ? 'Acknowledged' : 'Pending' }}
                    </span>
                    @if(!$alert->is_acknowledged)
                    <button wire:click="acknowledgeAlert({{ $alert->id }})" class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                        Acknowledge
                    </button>
                    @endif
                </div>
            </div>

            @if($alert->is_acknowledged && $alert->acknowledged_at)
            <div class="mt-3 text-xs text-gray-500">
                Acknowledged on: {{ $alert->acknowledged_at->format('Y-m-d H:i') }}
            </div>
            @endif
        </div>
        @empty
        <div class="p-4 bg-green-100 text-green-800 rounded text-center">
            No {{ $showPendingOnly ? 'pending' : '' }} alerts at this time
        </div>
        @endforelse
    </div>

    <div class="flex justify-between items-center">
        <div>
            Showing {{ $alerts->firstItem() ?? 0 }}-{{ $alerts->lastItem() ?? 0 }} of {{ $alerts->total() }}
        </div>
        {{ $alerts->links('pagination::tailwind') }}
    </div>

    @if(session('message'))
        <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif
</div>
