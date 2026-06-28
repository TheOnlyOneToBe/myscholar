<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Justification Management</h2>
    </div>

    <div class="flex gap-2">
        <select wire:model="statusFilter" class="px-4 py-2 border rounded">
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <select wire:model="perPage" class="px-4 py-2 border rounded">
            <option value="10">10 per page</option>
            <option value="25">25 per page</option>
            <option value="50">50 per page</option>
        </select>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @foreach($justifications as $justification)
        <div class="p-4 border rounded shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-bold text-lg">{{ $justification->student->first_name ?? 'Student' }}</div>
                    <div class="text-sm text-gray-600 mt-1">Reason: {{ Str::limit($justification->reason, 100) }}</div>
                    <div class="text-xs text-gray-500 mt-2">Submitted: {{ $justification->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="px-3 py-1 rounded text-white text-center
                        {{ $justification->status === 'pending' ? 'bg-yellow-500' : '' }}
                        {{ $justification->status === 'approved' ? 'bg-green-500' : '' }}
                        {{ $justification->status === 'rejected' ? 'bg-red-500' : '' }}
                    ">
                        {{ ucfirst($justification->status) }}
                    </span>
                </div>
            </div>

            @if($justification->status === 'pending')
            <div class="flex gap-2 mt-4">
                <button wire:click="approve({{ $justification->id }})" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                    Approve
                </button>
                <button wire:click="openResponseModal({{ $justification->id }})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                    Reject
                </button>
            </div>
            @endif

            @if($justification->rejection_reason)
            <div class="mt-4 p-3 bg-red-50 rounded text-sm">
                <strong>Rejection Reason:</strong> {{ $justification->rejection_reason }}
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="flex justify-between items-center">
        <div>
            Showing {{ $justifications->firstItem() ?? 0 }}-{{ $justifications->lastItem() ?? 0 }} of {{ $justifications->total() }}
        </div>
        {{ $justifications->links('pagination::tailwind') }}
    </div>

    @if(session('message'))
        <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif
</div>
