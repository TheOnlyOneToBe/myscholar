<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class TestAlertController extends Controller
{
    public function showForm()
    {
        return view('test-alerts');
    }

    public function triggerSuccess(): RedirectResponse
    {
        alert_success(
            'This is a success message!',
            'TEST_SUCCESS'
        );

        return redirect()->back();
    }

    public function triggerWarning(): RedirectResponse
    {
        alert_warning(
            'This is a warning message!',
            'TEST_WARNING'
        );

        return redirect()->back();
    }

    public function triggerError(): RedirectResponse
    {
        alert_error(
            'This is an error message!',
            'TEST_ERROR'
        );

        return redirect()->back();
    }

    public function triggerMultiple(): RedirectResponse
    {
        alert_success('Operation started successfully', 'OP_STARTED')
            ->warning('Check your email for confirmation', 'CONFIRM_EMAIL')
            ->warning('You have 24 hours to confirm', 'CONFIRM_TIMEOUT')
            ->error('Backup process encountered an issue', 'BACKUP_ERROR');

        return redirect()->back();
    }
}
