<?php

namespace Modules\Auth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;

class RoleController extends Controller
{
    /**
     * Get all roles
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.view_roles')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $roles = Role::where('is_active', true)
            ->with('permissions')
            ->orderBy('hierarchy_level', 'asc')
            ->get();

        return response()->json(['roles' => $roles]);
    }

    /**
     * Get role details
     */
    public function show(Role $role, Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.view_roles')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json([
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Get permissions for a role
     */
    public function permissions(Role $role, Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.view_permissions')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json([
            'permissions' => $role->permissions()->get(),
        ]);
    }

    /**
     * Assign permissions to role (admin only)
     */
    public function givePermissions(Role $role, Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.manage_permissions')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->syncWithoutDetaching($validated['permission_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Permissions assignées',
            'permissions' => $role->permissions()->get(),
        ]);
    }

    /**
     * Remove permissions from role (admin only)
     */
    public function revokePermissions(Role $role, Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.manage_permissions')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->detach($validated['permission_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Permissions retirées',
            'permissions' => $role->permissions()->get(),
        ]);
    }
}
