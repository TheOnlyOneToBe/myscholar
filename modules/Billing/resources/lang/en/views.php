<?php

return [
    'buttons' => [
        'create_invoice' => 'Create Invoice',
        'pay_now' => 'Pay Now',
        'record_payment' => 'Record Payment',
        'download_invoice' => 'Download Invoice',
        'print_invoice' => 'Print Invoice',
        'send_invoice' => 'Send Invoice',
        'create_payment_plan' => 'Create Payment Plan',
        'award_scholarship' => 'Award Scholarship',
        'approve_waiver' => 'Approve Waiver',
        'reject_waiver' => 'Reject Waiver',
        'view_details' => 'View Details',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'back' => 'Back',
    ],

    'labels' => [
        'invoice_number' => 'Invoice Number',
        'invoice_date' => 'Invoice Date',
        'due_date' => 'Due Date',
        'student_name' => 'Student Name',
        'student_id' => 'Student ID',
        'amount' => 'Amount',
        'paid_amount' => 'Amount Paid',
        'outstanding_balance' => 'Outstanding Balance',
        'payment_date' => 'Payment Date',
        'payment_method' => 'Payment Method',
        'reference' => 'Reference',
        'status' => 'Status',
        'fee_type' => 'Fee Type',
        'academic_year' => 'Academic Year',
    ],

    'placeholders' => [
        'search_invoices' => 'Search invoices...',
        'search_students' => 'Search students...',
        'select_payment_method' => 'Select payment method',
        'select_fee_type' => 'Select fee type',
        'select_student' => 'Select a student',
        'enter_amount' => 'Enter amount',
        'enter_reference' => 'Enter reference',
    ],

    'tables' => [
        'student' => 'Student',
        'invoice_number' => 'Invoice Number',
        'amount' => 'Amount',
        'paid' => 'Paid',
        'balance' => 'Balance',
        'due_date' => 'Due Date',
        'status' => 'Status',
        'actions' => 'Actions',
        'date' => 'Date',
    ],

    'sections' => [
        'my_invoices' => 'My Invoices',
        'invoices_list' => 'Invoice List',
        'payment_history' => 'Payment History',
        'outstanding_balance' => 'Outstanding Balance',
        'manage_fees' => 'Manage Fees',
        'manage_scholarships' => 'Manage Scholarships',
        'manage_waivers' => 'Manage Waivers',
        'payment_plans' => 'Payment Plans',
        'billing_summary' => 'Billing Summary',
        'billing_report' => 'Billing Report',
    ],

    'forms' => [
        'create_invoice' => 'Create Invoice',
        'record_payment' => 'Record Payment',
        'create_fee_structure' => 'Create Fee Structure',
        'award_scholarship' => 'Award Scholarship',
        'request_waiver' => 'Request Waiver',
    ],

    'alerts' => [
        'invoice_created' => 'Invoice created successfully',
        'invoice_sent' => 'Invoice sent successfully',
        'payment_recorded' => 'Payment recorded successfully',
        'payment_confirmed' => 'Payment confirmed',
        'scholarship_awarded' => 'Scholarship awarded successfully',
        'waiver_approved' => 'Waiver approved',
        'waiver_rejected' => 'Waiver rejected',
        'confirm_payment' => 'Confirm payment?',
        'invoice_overdue' => 'This invoice is overdue',
        'payment_plan_created' => 'Payment plan created successfully',
    ],

    'statuses' => [
        'unpaid' => 'Unpaid',
        'partially_paid' => 'Partially Paid',
        'fully_paid' => 'Fully Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
        'draft' => 'Draft',
        'sent' => 'Sent',
    ],

    'payment_methods' => [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'bank_transfer' => 'Bank Transfer',
        'online' => 'Online',
        'mobile_money' => 'Mobile Money',
    ],

    'fee_types' => [
        'tuition' => 'Tuition Fees',
        'registration' => 'Registration Fees',
        'examination' => 'Examination Fees',
        'activity' => 'Activity Fees',
        'library' => 'Library Fees',
        'sports' => 'Sports Fees',
        'uniform' => 'Uniform Fees',
    ],

    'empty_states' => [
        'no_invoices' => 'No invoices',
        'no_payments' => 'No payments recorded',
        'no_scholarships' => 'No scholarships',
        'no_waivers' => 'No waivers',
        'no_payment_plans' => 'No payment plans',
    ],

    'billing_summary' => [
        'total_fees' => 'Total Fees',
        'total_paid' => 'Total Paid',
        'total_outstanding' => 'Total Outstanding',
        'scholarship_discount' => 'Scholarship Discount',
        'waiver_discount' => 'Waiver Discount',
        'net_amount' => 'Net Amount',
        'payment_status' => 'Payment Status',
    ],
];
