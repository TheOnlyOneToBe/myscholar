<div class="space-y-6 p-6">
    <h1 class="text-2xl font-bold text-gray-900">Grade Appeals</h1>

    <div class="bg-white rounded-lg shadow p-4">
        <select 
            wire:model.live="filterStatus"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="space-y-4">
        @forelse($appeals as $appeal)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $appeal->student->first_name }} {{ $appeal->student->last_name }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Subject: {{ $appeal->subject->name }}</p>
                        <p class="text-sm text-gray-600">Original Grade: {{ $appeal->grade->score }}/20</p>
                        <p class="text-gray-700 mt-3">{{ $appeal->reason }}</p>
                        @if($appeal->response)
                            <div class="mt-4 p-3 bg-gray-50 rounded border-l-4 border-blue-500">
                                <p class="text-sm font-medium text-gray-600">Response:</p>
                                <p class="text-gray-700">{{ $appeal->response }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="ml-6 text-right">
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ 
                            $appeal->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            ($appeal->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')
                        }}">
                            {{ ucfirst($appeal->status) }}
                        </span>
                        @if($appeal->status === 'pending')
                            <div class="flex gap-2 mt-4">
                                <button 
                                    wire:click="openReviewModal({{ $appeal->id }}, 'approved')"
                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition"
                                >
                                    Approve
                                </button>
                                <button 
                                    wire:click="openReviewModal({{ $appeal->id }}, 'rejected')"
                                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition"
                                >
                                    Reject
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">No appeals found</p>
        @endforelse
    </div>

    {{ $appeals->links('pagination::tailwind') }}

    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
                <h2 class="text-xl font-bold mb-4">{{ ucfirst($reviewData['status']) }} Appeal</h2>
                <form wire:submit.prevent="submitReview" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Response</label>
                        <textarea 
                            wire:model="reviewData.response"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter your decision and comments..."
                        ></textarea>
                        @error('reviewData.response') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-2">
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        >
                            Submit
                        </button>
                        <button 
                            type="button"
                            wire:click="closeModal"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
