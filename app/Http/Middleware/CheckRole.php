<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (!$request->user()) {
            return redirect('login');
        }

        if (!$request->user()->hasRole($role)) {
            abort(403, "Rôle requis: {$role}");
        }

        return $next($request);
    }
}
