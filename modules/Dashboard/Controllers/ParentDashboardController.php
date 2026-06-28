<?php

namespace Modules\Dashboard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'parent']);
    }

    public function dashboard()
    {
        return view('dashboard::parent-dashboard');
    }

    public function profile()
    {
        $user = Auth::user();

        return view('dashboard::parent-profile', [
            'user' => $user,
        ]);
    }

    public function settings()
    {
        return view('dashboard::parent-settings');
    }
}
