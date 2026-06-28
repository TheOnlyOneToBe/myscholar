<?php

return [
    'invoice' => [
        'not_found' => 'Invoice not found',
        'already_exists' => 'This invoice already exists',
        'cannot_delete' => 'Cannot delete this invoice',
        'cannot_edit' => 'Cannot edit this invoice',
        'already_paid' => 'This invoice is already paid',
    ],

    'payment' => [
        'not_found' => 'Payment not found',
        'invalid_amount' => 'Invalid payment amount',
        'exceeds_balance' => 'Amount exceeds outstanding balance',
        'processing_failed' => 'Payment processing failed',
        'payment_method_invalid' => 'Invalid payment method',
        'cannot_process' => 'Cannot process this payment',
    ],

    'fee_structure' => [
        'not_found' => 'Fee structure not found',
        'already_exists' => 'This fee structure already exists',
        'cannot_delete' => 'Cannot delete this fee structure',
    ],

    'scholarship' => [
        'not_found' => 'Scholarship not found',
        'already_exists' => 'Scholarship already exists for this student',
        'cannot_apply' => 'Cannot apply this scholarship',
        'student_not_eligible' => 'Student is not eligible',
    ],

    'waiver' => [
        'not_found' => 'Waiver not found',
        'already_exists' => 'Waiver already exists',
        'cannot_approve' => 'Cannot approve this waiver',
        'exceeds_amount' => 'Amount exceeds total due',
    ],

    'payment_plan' => [
        'not_found' => 'Payment plan not found',
        'already_exists' => 'Payment plan already exists',
        'cannot_create' => 'Cannot create payment plan',
        'invalid_installments' => 'Invalid number of installments',
    ],

    'authorization' => [
        'unauthorized' => 'You do not have permission to access this data',
        'cannot_view' => 'You cannot view this invoice',
        'cannot_edit' => 'You cannot edit this invoice',
        'cannot_delete' => 'You cannot delete this invoice',
    ],

    'calculation' => [
        'calculation_error' => 'Error calculating amount',
        'rounding_error' => 'Rounding error',
    ],
];
