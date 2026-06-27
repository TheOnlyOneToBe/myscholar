<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Emploi du temps - {{ $this->class->name }}</h1>
            <p class="text-gray-600 mt-2">{{ $this->class->level }} - {{ $this->class->filiere }}</p>
        </div>
        <button wire:click="openForm()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Ajouter une session
        </button>
    </div>

    {{-- Calendrier Hebdomadaire --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700 w-24">
                        Heure
                    </th>
                    @foreach($days as $day)
                        <th class="border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700">
                            {{ __('classes.days.' . strtolower($day)) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($timeSlots as $slot)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-50">
                            {{ $slot }}
                        </td>
                        @foreach($days as $day)
                            <td class="border border-gray-300 px-4 py-2 text-center min-h-20">
                                @php
                                    $dayTimetables = $timetables->get($day, []);
                                    $session = $dayTimetables->first(function ($t) use ($slot) {
                                        return $t->start_time === $slot;
                                    });
                                @endphp

                                @if($session)
                                    <div class="bg-blue-100 border-l-4 border-blue-500 p-2 rounded text-left text-xs">
                                        <p class="font-semibold text-gray-900">{{ $session->subject_code }}</p>
                                        <p class="text-gray-600">{{ $session->teacher?->name ?? 'N/A' }}</p>
                                        <p class="text-gray-600">{{ $session->room?->name ?? 'N/A' }}</p>
                                        <p class="text-gray-500 text-xs mt-1">
                                            {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }}
                                        </p>
                                        <div class="mt-2 flex gap-1">
                                            <button wire:click="openForm({{ $session->id }})" class="text-blue-600 hover:text-blue-900 text-xs">
                                                ✏️
                                            </button>
                                            <button wire:click="confirmDelete({{ $session->id }})" class="text-red-600 hover:text-red-900 text-xs">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <button wire:click="openForm(null, '{{ $day }}')" class="text-gray-400 hover:text-gray-600 text-2xl w-full h-full hover:bg-gray-100 rounded">
                                        +
                                    </button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Formulaire --}}
    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Éditer la session' : 'Ajouter une session' }}
                </h3>

                <form wire:submit="saveTimetable">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jour</label>
                            <select wire:model="form.day_of_week" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option>Sélectionner</option>
                                @foreach($days as $d)
                                    <option value="{{ $d }}">{{ __('classes.days.' . strtolower($d)) }}</option>
                                @endforeach
                            </select>
                            @error('form.day_of_week') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Heure de début</label>
                            <input type="time" wire:model="form.start_time" class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('form.start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Heure de fin</label>
                            <input type="time" wire:model="form.end_time" class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('form.end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Code matière</label>
                            <input type="text" wire:model="form.subject_code" class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('form.subject_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Enseignant</label>
                            <select wire:model="form.user_id" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="">Sélectionner</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Salle</label>
                            <select wire:model="form.room_id" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="">Sélectionner</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type de session</label>
                            <select wire:model="form.session_type" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="regular">Cours régulier</option>
                                <option value="exam">Examen</option>
                                <option value="makeup">Rattrapage</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea wire:model="form.notes" class="w-full border border-gray-300 rounded px-3 py-2" rows="2"></textarea>
                        </div>

                        <div class="flex gap-2 pt-4">
                            <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Enregistrer
                            </button>
                            <button type="button" wire:click="closeForm" class="flex-1 bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Confirmation suppression --}}
    @if($deleteConfirm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la suppression</h3>
                <p class="text-gray-600 mb-4">Êtes-vous sûr de vouloir supprimer cette session?</p>
                <div class="flex gap-2">
                    <button wire:click="deleteTimetable" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Supprimer
                    </button>
                    <button wire:click="cancelDelete" class="flex-1 bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
