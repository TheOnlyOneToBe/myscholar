<?php

namespace Modules\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: Route::middleware('check.role:admin,proviseur')
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $allowedRoles = explode(',', $roles);
        $hasRole = $user->hasAnyRole($allowedRoles);

        if (!$hasRole) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return $next($request);
    }
}
