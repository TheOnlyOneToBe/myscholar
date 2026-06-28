<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold">Bulk Attendance Marking</h2>
        @if($session)
            <div class="text-right">
                <div class="text-lg font-semibold">{{ $class->name ?? 'Class' }}</div>
                <div class="text-sm text-gray-600">{{ $session->date->format('Y-m-d') }}</div>
            </div>
        @endif
    </div>

    @if(session('message'))
        <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
    @endif

    @if(!$session)
        <div class="p-6 bg-gray-100 rounded-lg">
            <p class="text-gray-600">No session selected. Please select a session first.</p>
        </div>
    @else
        <!-- Controls -->
        <div class="bg-white p-4 rounded-lg shadow space-y-4">
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Default Status</label>
                    <select wire:model="defaultStatus" class="w-full px-3 py-2 border rounded-lg">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="excused">Excused</option>
                        <option value="justified">Justified</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button wire:click="markAllPresent" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 w-full">
                        All Present
                    </button>
                </div>
                <div class="flex items-end gap-2">
                    <button wire:click="markAllAbsent" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 w-full">
                        All Absent
                    </button>
                </div>
                <div class="flex items-end gap-2">
                    <button wire:click="openConfirmation" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full font-semibold">
                        Submit
                    </button>
                </div>
            </div>

            <!-- CSV Import/Export -->
            <div class="flex gap-4 border-t pt-4">
                <button wire:click="exportAsCSV" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                    📥 Export CSV
                </button>
                <form wire:submit.prevent="importCSV" class="flex gap-2">
                    <input type="file" accept=".csv" name="csv_file" class="flex-1 px-3 py-2 border rounded-lg">
                    <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                        📤 Import CSV
                    </button>
                </form>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-3 gap-4 border-t pt-4">
                <div class="p-2 bg-blue-50 rounded">
                    <div class="text-2xl font-bold text-blue-600">{{ count($students) }}</div>
                    <div class="text-xs text-gray-600">Total Students</div>
                </div>
                <div class="p-2 bg-green-50 rounded">
                    <div class="text-2xl font-bold text-green-600">
                        {{ count(array_filter($attendance, fn($a) => $a['status'] === 'present')) }}
                    </div>
                    <div class="text-xs text-gray-600">Present</div>
                </div>
                <div class="p-2 bg-red-50 rounded">
                    <div class="text-2xl font-bold text-red-600">
                        {{ count(array_filter($attendance, fn($a) => $a['status'] === 'absent')) }}
                    </div>
                    <div class="text-xs text-gray-600">Absent</div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3 text-left">Student Name</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-left">Notes</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($students as $student)
                        @php
                            $studentId = $student['id'];
                            $status = $attendance[$studentId]['status'] ?? 'present';
                            $notes = $attendance[$studentId]['notes'] ?? '';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-3">
                                <div class="font-medium">{{ $student['first_name'] }} {{ $student['last_name'] }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $studentId }}</div>
                            </td>
                            <td class="p-3">
                                <select wire:change="setStatus({{ $studentId }}, $event.target.value)"
                                    class="px-2 py-1 border rounded text-sm w-full
                                        {{ $status === 'present' ? 'bg-green-50' : '' }}
                                        {{ $status === 'absent' ? 'bg-red-50' : '' }}
                                        {{ $status === 'late' ? 'bg-yellow-50' : '' }}
                                        {{ $status === 'excused' ? 'bg-blue-50' : '' }}
                                    ">
                                    <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="excused" {{ $status === 'excused' ? 'selected' : '' }}>Excused</option>
                                    <option value="justified" {{ $status === 'justified' ? 'selected' : '' }}>Justified</option>
                                </select>
                            </td>
                            <td class="p-3">
                                <input type="text"
                                    wire:change="setNotes({{ $studentId }}, $event.target.value)"
                                    value="{{ $notes }}"
                                    placeholder="Add notes..."
                                    maxlength="100"
                                    class="w-full px-2 py-1 border rounded text-sm">
                            </td>
                            <td class="p-3 text-center">
                                <button wire:click="toggleStatus({{ $studentId }})"
                                    class="px-2 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">
                                    Toggle
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Confirmation Modal -->
        @if($showConfirmation)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4 space-y-4">
                    <h3 class="text-xl font-bold">Confirm Bulk Attendance</h3>

                    <div class="p-4 bg-gray-100 rounded space-y-2">
                        <div>Total students: <strong>{{ count($students) }}</strong></div>
                        <div>Present: <strong class="text-green-600">
                            {{ count(array_filter($attendance, fn($a) => $a['status'] === 'present')) }}
                        </strong></div>
                        <div>Absent: <strong class="text-red-600">
                            {{ count(array_filter($attendance, fn($a) => $a['status'] === 'absent')) }}
                        </strong></div>
                        <div>Other: <strong class="text-yellow-600">
                            {{ count($students) - count(array_filter($attendance, fn($a) => in_array($a['status'], ['present', 'absent']))) }}
                        </strong></div>
                    </div>

                    <p class="text-gray-600 text-sm">Are you sure you want to submit this attendance data?</p>

                    <div class="flex gap-4">
                        <button wire:click="closeConfirmation" class="flex-1 px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button wire:click="submitBulkAttendance"
                            wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                            <span wire:loading.remove>Confirm & Submit</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Result -->
        @if($bulkResult)
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <h4 class="font-bold text-green-800 mb-2">Bulk Submission Complete</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $bulkResult['success'] }}</div>
                        <div class="text-sm text-gray-600">Successfully marked</div>
                    </div>
                    @if($bulkResult['failed'] > 0)
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{ $bulkResult['failed'] }}</div>
                            <div class="text-sm text-gray-600">Failed to mark</div>
                        </div>
                    @endif
                </div>
                @if(!empty($bulkResult['errors']))
                    <div class="mt-4 p-3 bg-red-50 rounded text-sm">
                        <strong>Errors:</strong>
                        <ul class="list-disc list-inside">
                            @foreach($bulkResult['errors'] as $error)
                                <li>Student {{ $error['student_id'] }}: {{ $error['error'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
