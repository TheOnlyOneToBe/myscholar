<div class="space-y-6 p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Subjects</h1>
        <button 
            wire:click="openModal"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
        >
            Add Subject
        </button>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <input 
            type="text" 
            placeholder="Search subjects..." 
            wire:model.live.debounce="search"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($subjects as $subject)
            <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">{{ $subject->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $subject->code }}</p>
                        <p class="text-sm text-gray-600 mt-2">Credits: {{ $subject->credits }}</p>
                        <p class="text-sm text-gray-600">Coefficient: {{ $subject->coefficient }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $subject->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex gap-2 mt-4">
                    <button 
                        wire:click="edit({{ $subject->id }})"
                        class="flex-1 px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200 transition"
                    >
                        Edit
                    </button>
                    <button 
                        wire:click="delete({{ $subject->id }})"
                        class="flex-1 px-3 py-1 bg-red-100 text-red-700 rounded text-sm hover:bg-red-200 transition"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-full text-center py-8">No subjects found</p>
        @endforelse
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-700">Showing {{ $subjects->total() }} subjects</span>
        {{ $subjects->links('pagination::tailwind') }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
                <h2 class="text-xl font-bold mb-4">{{ $editingId ? 'Edit' : 'Add' }} Subject</h2>
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Code</label>
                        <input 
                            type="text" 
                            wire:model="formData.code"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                        @error('formData.code') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input 
                            type="text" 
                            wire:model="formData.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                        @error('formData.name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Coefficient</label>
                        <input 
                            type="number" 
                            step="0.1"
                            wire:model="formData.coefficient"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="formData.is_active"
                            id="is_active"
                            class="rounded border-gray-300"
                        >
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
                    </div>
                    <div class="flex gap-2">
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        >
                            Save
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
