<?php

namespace Modules\Dashboard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\ParentMiddleware;

class ParentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', ParentMiddleware::class]);
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
