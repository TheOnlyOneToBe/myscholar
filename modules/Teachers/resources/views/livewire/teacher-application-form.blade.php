<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ __('teachers::views.titles.teacher_application_form') }}</h1>

            @if($message)
                <div class="mb-6 p-4 rounded-lg {{ $messageType === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                    {{ $message }}
                </div>
            @endif

            <form wire:submit="submit" class="space-y-6">
                <!-- Section: Informations de base -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('teachers::views.sections.basic_info') }}</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.specialization') }}</label>
                            <input type="text" wire:model="specialization" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="{{ __('teachers::views.placeholders.specialization') }}">
                            @error('specialization') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.qualification_level') }}</label>
                            <select wire:model="qualification_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('teachers::views.buttons.search') }}</option>
                                <option value="Bac+2">{{ __('teachers::views.qualification_levels.bac2') }}</option>
                                <option value="Bac+3">{{ __('teachers::views.qualification_levels.bac3') }}</option>
                                <option value="Bac+5">{{ __('teachers::views.qualification_levels.bac5') }}</option>
                                <option value="Doctorat">{{ __('teachers::views.qualification_levels.doctorate') }}</option>
                            </select>
                            @error('qualification_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.hire_date') }}</label>
                            <input type="date" wire:model="hire_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('hire_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.filiere') }}</label>
                            <select wire:model="filiere" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('teachers::views.buttons.search') }}</option>
                                <option value="generale">{{ __('teachers::views.fieres.generale') }}</option>
                                <option value="technique">{{ __('teachers::views.fieres.technique') }}</option>
                            </select>
                            @error('filiere') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.years_of_experience') }}</label>
                            <input type="number" wire:model.number="years_of_experience" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('years_of_experience') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.office_location') }}</label>
                            <input type="text" wire:model="office_location" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="{{ __('teachers::views.placeholders.office_location') }}">
                            @error('office_location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.biography') }}</label>
                        <textarea wire:model="bio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="4" placeholder="{{ __('teachers::views.placeholders.bio') }}"></textarea>
                        @error('bio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Section: Contacts -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('teachers::views.sections.contact_info') }}</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.phone_office') }}</label>
                            <input type="tel" wire:model="phone_office" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="{{ __('teachers::views.placeholders.phone') }}">
                            @error('phone_office') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.email_office') }}</label>
                            <input type="email" wire:model="email_office" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="nom@lycee.cm">
                            @error('email_office') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Matières -->
                <div class="pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('teachers::views.sections.subjects') }}</h2>

                    @error('selectedSubjects') <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-lg">{{ $message }}</div> @enderror

                    <!-- Matières sélectionnées -->
                    @if($selectedSubjects)
                        <div class="mb-6 space-y-3">
                            @foreach($selectedSubjects as $subjectId)
                                @php
                                    $subject = $subjects->firstWhere('id', $subjectId);
                                @endphp
                                @if($subject)
                                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $subject->name }}</p>
                                            <div class="grid grid-cols-3 gap-4 mt-2 text-sm">
                                                <div>
                                                    <label class="text-gray-600">{{ __('teachers::views.messages.subject_level') }}:</label>
                                                    <select wire:model="subjectProficiency.{{ $subjectId }}" class="mt-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                                        <option value="1">{{ __('teachers::views.proficiency_levels.1') }}</option>
                                                        <option value="2">{{ __('teachers::views.proficiency_levels.2') }}</option>
                                                        <option value="3">{{ __('teachers::views.proficiency_levels.3') }}</option>
                                                        <option value="4">{{ __('teachers::views.proficiency_levels.4') }}</option>
                                                        <option value="5">{{ __('teachers::views.proficiency_levels.5') }}</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-gray-600">{{ __('teachers::views.labels.since') }}:</label>
                                                    <input type="number" wire:model.number="subjectSinceYear.{{ $subjectId }}" min="1900" max="{{ now()->year }}" class="mt-1 px-2 py-1 border border-gray-300 rounded text-sm w-full">
                                                </div>
                                                <div class="flex items-end">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" wire:change="setPrimary({{ $subjectId }})" @checked($subjectIsPrimary[$subjectId] ?? false) class="rounded">
                                                        <span class="ml-2 text-gray-700">{{ __('teachers::views.labels.primary_specialty') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="removeSubject({{ $subjectId }})" class="ml-4 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Ajouter une matière -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('teachers::views.sections.add_subject_section') }}</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.subject_name') }}</label>
                                <select wire:model="newSubjectId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">{{ __('teachers::views.messages.select_subject_to_add') }}</option>
                                    @foreach($subjects as $subject)
                                        @if(!in_array($subject->id, $selectedSubjects))
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.proficiency_level') }}</label>
                                <select wire:model.number="newProficiency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="1">{{ __('teachers::views.proficiency_levels.1') }}</option>
                                    <option value="2">{{ __('teachers::views.proficiency_levels.2') }}</option>
                                    <option value="3">{{ __('teachers::views.proficiency_levels.3') }}</option>
                                    <option value="4">{{ __('teachers::views.proficiency_levels.4') }}</option>
                                    <option value="5">{{ __('teachers::views.proficiency_levels.5') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('teachers::views.labels.since_year') }}</label>
                                <input type="number" wire:model.number="newSinceYear" min="1900" max="{{ now()->year }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.boolean="newIsPrimary" class="rounded">
                                <span class="ml-2 text-gray-700">{{ __('teachers::views.labels.primary_specialty') }}</span>
                            </label>
                        </div>
                        <button type="button" wire:click="addSubject" class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-plus mr-2"></i>{{ __('teachers::views.buttons.add_subject') }}
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        <i class="fas fa-paper-plane mr-2"></i>{{ __('teachers::views.buttons.submit_application') }}
                    </button>
                    <a href="/" class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition text-center">
                        <i class="fas fa-times mr-2"></i>{{ __('teachers::views.buttons.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
