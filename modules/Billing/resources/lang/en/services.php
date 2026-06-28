<?php

return [
    'invoice_service' => [
        'generating' => 'Generating invoice...',
        'creating' => 'Creating invoice...',
        'updating' => 'Updating invoice...',
        'deleting' => 'Deleting invoice...',
        'sending' => 'Sending invoice...',
        'generated_success' => 'Invoice generated successfully',
        'created_success' => 'Invoice created successfully',
        'updated_success' => 'Invoice updated successfully',
        'deleted_success' => 'Invoice deleted successfully',
        'sent_success' => 'Invoice sent successfully',
        'calculating_totals' => 'Calculating totals...',
    ],

    'payment_service' => [
        'processing' => 'Processing payment...',
        'confirming' => 'Confirming payment...',
        'recording' => 'Recording payment...',
        'verifying' => 'Verifying payment...',
        'processed_success' => 'Payment processed successfully',
        'confirmed_success' => 'Payment confirmed',
        'recorded_success' => 'Payment recorded successfully',
        'cancelled' => 'Payment cancelled',
    ],

    'fee_structure_service' => [
        'creating' => 'Creating fee structure...',
        'updating' => 'Updating fee structure...',
        'deleting' => 'Deleting fee structure...',
        'applying' => 'Applying fees...',
        'created_success' => 'Fee structure created successfully',
        'updated_success' => 'Fee structure updated successfully',
        'applied_success' => 'Fees applied successfully',
    ],

    'scholarship_service' => [
        'awarding' => 'Awarding scholarship...',
        'creating' => 'Creating scholarship...',
        'updating' => 'Updating scholarship...',
        'awarding_success' => 'Scholarship awarded successfully',
        'created_success' => 'Scholarship created successfully',
        'checking_eligibility' => 'Checking eligibility...',
    ],

    'waiver_service' => [
        'creating' => 'Creating waiver...',
        'approving' => 'Approving waiver...',
        'rejecting' => 'Rejecting waiver...',
        'created_success' => 'Waiver created successfully',
        'approved_success' => 'Waiver approved',
        'rejected_success' => 'Waiver rejected',
    ],

    'payment_plan_service' => [
        'creating' => 'Creating payment plan...',
        'updating' => 'Updating payment plan...',
        'calculating' => 'Calculating installments...',
        'created_success' => 'Payment plan created successfully',
        'updated_success' => 'Payment plan updated successfully',
    ],

    'report_service' => [
        'generating' => 'Generating report...',
        'fetching_data' => 'Fetching data...',
        'calculating_stats' => 'Calculating statistics...',
        'generated_success' => 'Report generated successfully',
    ],
];
