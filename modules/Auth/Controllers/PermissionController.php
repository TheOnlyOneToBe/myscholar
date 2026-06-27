<?php

namespace Modules\Auth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Get all permissions
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.view_permissions')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $query = Permission::where('is_active', true);

        // Filter by module if provided
        if ($request->has('module')) {
            $query->where('module', $request->input('module'));
        }

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by scope if provided
        if ($request->has('scope')) {
            $query->where('scope', $request->input('scope'));
        }

        $permissions = $query->orderBy('module', 'asc')
            ->orderBy('permission_id', 'asc')
            ->get();

        return response()->json(['permissions' => $permissions]);
    }

    /**
     * Get permission details
     */
    public function show(Permission $permission, Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.view_permissions')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json([
            'permission' => $permission->load('roles'),
        ]);
    }

    /**
     * Get permissions by module
     */
    public function byModule(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.view_permissions')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $module = $request->input('module');
        if (!$module) {
            return response()->json(['message' => 'Module parameter required'], 422);
        }

        $permissions = Permission::where('module', $module)
            ->where('is_active', true)
            ->orderBy('category', 'asc')
            ->orderBy('permission_id', 'asc')
            ->get();

        return response()->json(['permissions' => $permissions]);
    }

    /**
     * Get user's permissions
     */
    public function userPermissions(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $permissions = $user->getPermissions();

        return response()->json([
            'permissions' => $permissions,
        ]);
    }

    /**
     * Check if user has specific permission
     */
    public function checkPermission(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $validated = $request->validate([
            'permission_id' => 'required|string',
        ]);

        $hasPermission = $user->hasPermission($validated['permission_id']);

        return response()->json([
            'has_permission' => $hasPermission,
            'permission_id' => $validated['permission_id'],
        ]);
    }
}
