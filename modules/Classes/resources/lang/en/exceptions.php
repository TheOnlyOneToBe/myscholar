<?php

return [
    'class' => [
        'not_found' => 'Class not found',
        'already_exists' => 'This class already exists',
        'invalid_class' => 'Invalid class',
        'cannot_delete' => 'Cannot delete this class',
        'has_students' => 'This class still has students',
    ],

    'assignment' => [
        'not_found' => 'Assignment not found',
        'already_exists' => 'This student is already assigned to this class',
        'capacity_exceeded' => 'Class capacity exceeded',
        'cannot_assign' => 'Cannot assign this student',
        'invalid_assignment' => 'Invalid assignment',
    ],

    'timetable' => [
        'not_found' => 'Timetable not found',
        'duplicate_entry' => 'This timetable entry already exists',
        'conflict' => 'Timetable conflict detected',
        'teacher_busy' => 'Teacher is busy at this time',
        'room_unavailable' => 'Room is not available at this time',
        'invalid_time_slot' => 'Invalid time slot',
    ],

    'room' => [
        'not_found' => 'Room not found',
        'already_assigned' => 'This room is already assigned',
        'cannot_delete' => 'Cannot delete this room',
        'insufficient_capacity' => 'Insufficient capacity for this class',
    ],

    'subject' => [
        'not_found' => 'Subject not found',
        'already_added' => 'This subject is already added to the class',
        'teacher_not_found' => 'Teacher not found',
        'cannot_remove' => 'Cannot remove this subject',
    ],

    'academic_period' => [
        'not_found' => 'Academic period not found',
        'invalid_period' => 'Invalid period',
        'closed' => 'This academic period is closed',
    ],

    'authorization' => [
        'unauthorized' => 'You do not have permission to access this class',
        'cannot_edit' => 'You cannot edit this class',
        'cannot_delete' => 'You cannot delete this class',
        'cannot_assign' => 'You cannot assign students',
    ],
];
