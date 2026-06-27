<?php

namespace Modules\Auth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Services\UserManagementService;
use Modules\Auth\Requests\CreateUserRequest;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private UserManagementService $userManagementService,
    ) {}

    /**
     * Get all users (with permission check)
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('auth.view_users')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $users = User::where('is_active', true)
            ->with('currentRoles.role')
            ->paginate(20);

        return response()->json($users);
    }

    /**
     * Create a new user
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        $result = $this->userManagementService->createUser(
            $request->validated(),
            $user
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'user' => $result['user']->load('currentRoles.role'),
            'message' => $result['message'],
        ], 201);
    }

    /**
     * Get user details
     */
    public function show(User $user, Request $request): JsonResponse
    {
        $authUser = auth('sanctum')->user();
        if (!$authUser || !$authUser->hasPermission('auth.view_users')) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json([
            'user' => $user->load('currentRoles.role'),
        ]);
    }

    /**
     * Update user
     */
    public function update(User $user, Request $request): JsonResponse
    {
        $authUser = auth('sanctum')->user();

        $validated = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $result = $this->userManagementService->updateUser(
            $user,
            $validated,
            $authUser
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'user' => $result['user']->load('currentRoles.role'),
        ]);
    }

    /**
     * Assign role to user
     */
    public function assignRole(User $user, Request $request): JsonResponse
    {
        $authUser = auth('sanctum')->user();

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'reason' => 'nullable|string|max:255',
            'ends_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $role = Role::find($validated['role_id']);

        $result = $this->userManagementService->assignRole(
            $user,
            $role,
            $authUser,
            $validated['reason'] ?? null,
            $validated['ends_at'] ? new \Carbon\Carbon($validated['ends_at']) : null
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'user_role' => $result['user_role'],
            'message' => $result['message'],
        ]);
    }

    /**
     * Remove role from user
     */
    public function removeRole(User $user, Request $request): JsonResponse
    {
        $authUser = auth('sanctum')->user();

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($validated['role_id']);

        $result = $this->userManagementService->removeRole(
            $user,
            $role,
            $authUser
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * Deactivate user
     */
    public function deactivate(User $user, Request $request): JsonResponse
    {
        $authUser = auth('sanctum')->user();

        $result = $this->userManagementService->deactivateUser($user, $authUser);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * Activate user
     */
    public function activate(User $user, Request $request): JsonResponse
    {
        $authUser = auth('sanctum')->user();

        $result = $this->userManagementService->activateUser($user, $authUser);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}
