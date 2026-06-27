<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PermissionService;

class CheckPermission
{
    public function __construct(protected PermissionService $permissionService)
    {
    }

    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (!$request->user()) {
            return redirect('login');
        }

        if (!$this->permissionService->hasPermission($request->user(), $permission)) {
            abort(403, "Non autorisé: {$permission}");
        }

        return $next($request);
    }
}
