<?php

return [
    'buttons' => [
        'record_attendance' => 'Record Attendance',
        'mark_present' => 'Mark Present',
        'mark_absent' => 'Mark Absent',
        'mark_late' => 'Mark Late',
        'justify_absence' => 'Justify Absence',
        'submit_justification' => 'Submit Justification',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'view_details' => 'View Details',
        'download_report' => 'Download Report',
        'upload_document' => 'Upload Document',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'back' => 'Back',
    ],

    'labels' => [
        'student' => 'Student',
        'date' => 'Date',
        'status' => 'Status',
        'session' => 'Session',
        'subject' => 'Subject',
        'time' => 'Time',
        'class' => 'Class',
        'reason' => 'Reason',
        'attendance_rate' => 'Attendance Rate',
        'total_absences' => 'Total Absences',
        'consecutive_absences' => 'Consecutive Absences',
        'document' => 'Document',
        'submission_date' => 'Submission Date',
        'approval_date' => 'Approval Date',
    ],

    'placeholders' => [
        'search_students' => 'Search students...',
        'search_sessions' => 'Search sessions...',
        'select_status' => 'Select status',
        'select_session' => 'Select a session',
        'enter_reason' => 'Enter reason...',
        'select_class' => 'Select class',
    ],

    'tables' => [
        'student' => 'Student',
        'date' => 'Date',
        'status' => 'Status',
        'subject' => 'Subject',
        'class' => 'Class',
        'actions' => 'Actions',
        'absences' => 'Absences',
        'attendance_rate' => 'Attendance Rate',
    ],

    'sections' => [
        'my_attendance' => 'My Attendance',
        'attendance_list' => 'Attendance List',
        'mark_attendance' => 'Mark Attendance',
        'class_attendance' => 'Class Attendance',
        'my_absences' => 'My Absences',
        'absence_history' => 'Absence History',
        'justifications' => 'Justifications',
        'pending_justifications' => 'Pending Justifications',
        'alerts' => 'Alerts',
        'attendance_report' => 'Attendance Report',
        'statistics' => 'Attendance Statistics',
    ],

    'forms' => [
        'record_attendance' => 'Record Attendance',
        'justify_absence' => 'Justify Absence',
        'create_session' => 'Create Session',
        'edit_session' => 'Edit Session',
    ],

    'alerts' => [
        'attendance_recorded' => 'Attendance recorded successfully',
        'attendance_updated' => 'Attendance updated successfully',
        'justification_submitted' => 'Justification submitted successfully',
        'justification_approved' => 'Justification approved',
        'justification_rejected' => 'Justification rejected',
        'confirm_mark_absent' => 'Are you sure you want to mark this student absent?',
        'confirm_delete' => 'Are you sure you want to delete this record?',
        'high_absence_warning' => 'This student has a high absence rate',
        'limit_reached' => 'Absence limit has been reached',
    ],

    'statuses' => [
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'excused' => 'Excused',
        'unexcused' => 'Unexcused',
        'pending' => 'Pending',
    ],

    'justification_statuses' => [
        'submitted' => 'Submitted',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'withdrawn' => 'Withdrawn',
    ],

    'empty_states' => [
        'no_attendance' => 'No attendance records',
        'no_absences' => 'No absences recorded',
        'no_justifications' => 'No justifications submitted',
        'no_sessions' => 'No sessions created',
        'no_alerts' => 'No alerts',
    ],

    'report_fields' => [
        'student_name' => 'Student Name',
        'class' => 'Class',
        'period' => 'Period',
        'total_sessions' => 'Total Sessions',
        'present_count' => 'Present Count',
        'absent_count' => 'Absent Count',
        'excused_count' => 'Excused Absences',
        'attendance_rate' => 'Attendance Rate',
        'date_generated' => 'Date Generated',
    ],
];
