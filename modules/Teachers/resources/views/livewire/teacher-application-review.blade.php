<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('teachers::views.titles.teacher_applications') }}</h1>
        </div>

        <!-- Filtres -->
        <div class="mb-6 flex gap-2">
            <button wire:click="$set('filter', 'all')" @class(['px-4 py-2 rounded-lg font-medium', 'bg-blue-600 text-white' => $filter === 'all', 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' => $filter !== 'all'])>
                <i class="fas fa-list mr-2"></i>{{ __('teachers::views.filters.all') }}
            </button>
            <button wire:click="$set('filter', 'pending')" @class(['px-4 py-2 rounded-lg font-medium', 'bg-yellow-600 text-white' => $filter === 'pending', 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' => $filter !== 'pending'])>
                <i class="fas fa-clock mr-2"></i>{{ __('teachers::views.filters.pending') }}
            </button>
            <button wire:click="$set('filter', 'approved')" @class(['px-4 py-2 rounded-lg font-medium', 'bg-green-600 text-white' => $filter === 'approved', 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' => $filter !== 'approved'])>
                <i class="fas fa-check-circle mr-2"></i>{{ __('teachers::views.filters.approved') }}
            </button>
            <button wire:click="$set('filter', 'rejected')" @class(['px-4 py-2 rounded-lg font-medium', 'bg-red-600 text-white' => $filter === 'rejected', 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' => $filter !== 'rejected'])>
                <i class="fas fa-times-circle mr-2"></i>{{ __('teachers::views.filters.rejected') }}
            </button>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <!-- Liste des candidatures -->
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-lg">
                    @forelse($applications as $application)
                        <div wire:click="selectApplication({{ $application->id }})" @class(['p-4 border-b cursor-pointer hover:bg-gray-50 transition', 'bg-blue-50' => $selectedApplication?->id === $application->id])>
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ $application->user->first_name }} {{ $application->user->last_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $application->specialization }} • {{ $application->years_of_experience }} {{ __('teachers::views.labels.experience_years') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ __('teachers::views.labels.submitted_on') }} {{ $application->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <span @class(['inline-block px-3 py-1 rounded-full text-sm font-medium',
                                    'bg-yellow-100 text-yellow-800' => $application->status === 'pending',
                                    'bg-green-100 text-green-800' => $application->status === 'approved',
                                    'bg-red-100 text-red-800' => $application->status === 'rejected'
                                ])>
                                    {{ __('teachers::views.statuses.' . $application->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            {{ __('teachers::views.messages.no_applications_found') }}
                        </div>
                    @endforelse
                </div>

                {{ $applications->links() }}
            </div>

            <!-- Détails de la candidature sélectionnée -->
            @if($selectedApplication)
                <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('teachers::views.sections.application_details') }}</h2>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-600">{{ __('teachers::views.labels.full_name') }}</p>
                            <p class="font-medium text-gray-900">{{ $selectedApplication->user->first_name }} {{ $selectedApplication->user->last_name }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600">{{ __('teachers::views.labels.email') }}</p>
                            <p class="font-medium text-gray-900">{{ $selectedApplication->user->email }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600">{{ __('teachers::views.labels.specialization') }}</p>
                            <p class="font-medium text-gray-900">{{ $selectedApplication->specialization }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600">{{ __('teachers::views.labels.qualification_level') }}</p>
                            <p class="font-medium text-gray-900">{{ $selectedApplication->qualification_level }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600">{{ __('teachers::views.labels.years_of_experience') }}</p>
                            <p class="font-medium text-gray-900">{{ $selectedApplication->years_of_experience }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600">{{ __('teachers::views.labels.filiere') }}</p>
                            <p class="font-medium text-gray-900">{{ __('teachers::views.fieres.' . $selectedApplication->filiere) }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600">{{ __('teachers::views.labels.biography') }}</p>
                            <p class="text-gray-800">{{ $selectedApplication->bio }}</p>
                        </div>

                        @if($selectedApplication->subjects_data)
                            <div>
                                <p class="text-gray-600 font-medium mb-2">{{ __('teachers::views.sections.declared_subjects') }}</p>
                                <div class="space-y-2">
                                    @foreach($selectedApplication->subjects_data as $subject)
                                        <div class="text-sm bg-gray-50 p-2 rounded">
                                            <p class="font-medium text-gray-900">{{ $subject['subject_id'] }}</p>
                                            <p class="text-gray-600">{{ __('teachers::views.messages.subject_level') }}: {{ $subject['proficiency_level'] }}/5 • {{ __('teachers::views.labels.since') }}: {{ $subject['since_year'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($selectedApplication->isPending())
                        <div class="mt-6 space-y-2 border-t pt-4">
                            <button wire:click="approveApplication({{ $selectedApplication->id }})" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                <i class="fas fa-check mr-2"></i>{{ __('teachers::views.buttons.approve') }}
                            </button>
                            <button wire:click="openRejectModal({{ $selectedApplication->id }})" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                                <i class="fas fa-times mr-2"></i>{{ __('teachers::views.buttons.reject') }}
                            </button>
                        </div>
                    @elseif($selectedApplication->isRejected() && $selectedApplication->rejection_reason)
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm font-medium text-red-900">{{ __('teachers::views.messages.rejection_reason_header') }}</p>
                            <p class="text-sm text-red-800">{{ $selectedApplication->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-lg shadow-lg p-8 text-center text-gray-500">
                    {{ __('teachers::views.messages.select_application') }}
                </div>
            @endif
        </div>

        <!-- Modal de rejet -->
        @if($showRejectModal && $selectedApplication)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md mx-4 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('teachers::views.modals.reject_application_title') }}</h3>

                    <form wire:submit="rejectApplication({{ $selectedApplication->id }})" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.rejection_reason') }}</label>
                            <textarea wire:model="rejectionReason" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="4" placeholder="{{ __('teachers::views.placeholders.rejection_reason') }}"></textarea>
                            @error('rejectionReason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="button" wire:click="$set('showRejectModal', false)" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                                <i class="fas fa-times mr-2"></i>{{ __('teachers::views.buttons.cancel') }}
                            </button>
                            <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                                <i class="fas fa-ban mr-2"></i>{{ __('teachers::views.buttons.confirm_rejection') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
