<?php

namespace Modules\Auth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Services\AuthService;
use Modules\Auth\Services\PasswordResetService;
use Modules\Auth\Requests\LoginRequest;
use Modules\Auth\Requests\ForgotPasswordRequest;
use Modules\Auth\Requests\ResetPasswordRequest;
use Modules\Auth\Requests\ChangePasswordRequest;
use Modules\Auth\Models\User;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private PasswordResetService $passwordResetService,
    ) {}

    /**
     * Login endpoint
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent', '');

        $result = $this->authService->login(
            $request->input('email_or_username'),
            $request->input('password'),
            $ipAddress,
            $userAgent
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $result['user'],
            'token' => $result['token'],
            'expires_at' => $result['expires_at'],
        ]);
    }

    /**
     * Logout endpoint
     */
    public function logout(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $this->authService->logout($user);

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        return response()->json([
            'user' => $user->load('currentRoles.role'),
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $result = $this->authService->changePassword(
            $user,
            $request->input('current_password'),
            $request->input('new_password')
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
     * Request password reset (forgot password)
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->createResetToken(
            $request->input('email')
        );

        // Toujours retourner un message success pour ne pas révéler si l'email existe
        return response()->json([
            'success' => true,
            'message' => 'Si cet email existe, un lien de réinitialisation sera envoyé',
        ]);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->resetPassword(
            $request->input('email'),
            $request->input('token'),
            $request->input('password')
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
     * Validate password reset token
     */
    public function validateResetToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $result = $this->passwordResetService->validateToken(
            $validated['email'],
            $validated['token']
        );

        if (!$result['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json($result);
    }
}
