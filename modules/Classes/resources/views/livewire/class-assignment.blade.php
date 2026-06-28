<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ __('classes::views.assignments.title') }} - {{ $class->name }}</h1>
        <button wire:click="openForm()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            {{ __('classes::views.assignments.add_teacher') }}
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($assignments as $assignment)
            <div class="border border-gray-300 rounded-lg p-4 bg-white">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold">{{ $assignment->teacher->name }}</h3>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $assignment->role }}</span>
                    </div>
                </div>
                
                @if($assignment->subject)
                    <p class="text-sm text-gray-600 mb-2"><strong>{{ __('classes::views.labels.subject') }}:</strong> {{ $assignment->subject }}</p>
                @endif

                <p class="text-xs text-gray-500 mb-3">{{ __('classes::views.labels.academic_year') }}: {{ $assignment->schoolYear->name }}</p>
                
                <div class="flex gap-2">
                    <button wire:click="openForm({{ $assignment->id }})" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-sm py-2 rounded">
                        {{ __('classes::views.assignments.edit_button') }}
                    </button>
                    <button wire:click="confirmDelete({{ $assignment->id }})" class="flex-1 bg-red-500 hover:bg-red-700 text-white text-sm py-2 rounded">
                        {{ __('classes::views.assignments.remove_button') }}
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-8 text-gray-500">
                {{ __('classes::views.assignments.no_teachers_assigned') }}
            </div>
        @endforelse
    </div>

    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? __('classes::views.assignments.edit_assignment') : __('classes::views.assignments.new_assignment') }}
                </h3>

                <form wire:submit="saveAssignment">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('classes::views.assignments.teacher_label') }}</label>
                            <select wire:model="form.user_id" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="">{{ __('classes::views.assignments.select_option') }}</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('form.user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('classes::views.assignments.role_label') }}</label>
                            <select wire:model="form.role" class="w-full border border-gray-300 rounded px-3 py-2">
                                @foreach($roles as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('classes::views.assignments.subject_label') }}</label>
                            <input type="text" wire:model="form.subject" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="{{ __('classes::views.assignments.subject_placeholder') }}">
                        </div>

                        <div class="flex gap-2 pt-4">
                            <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('classes::views.assignments.save_button') }}
                            </button>
                            <button type="button" wire:click="closeForm" class="flex-1 bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                {{ __('classes::views.assignments.cancel_button') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($deleteConfirm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-80 shadow-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('classes::views.assignments.confirm_remove') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('classes::views.assignments.confirm_remove_message') }}</p>
                <div class="flex gap-2">
                    <button wire:click="deleteAssignment" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('classes::views.assignments.yes_button') }}
                    </button>
                    <button wire:click="cancelDelete" class="flex-1 bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        {{ __('classes::views.assignments.no_button') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
