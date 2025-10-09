<?php

namespace App\Http\Controllers\Api;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        event(new UserRegistered($user));

        return $this->successResponse($user, 'User registered successfully', 201);
    }

    /**
     * Login a user and return an access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user,
        ], 'Login successful');
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): JsonResponse
    {
        $request = request();
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse('Logged out successfully');
    }

    /**
     * Get the authenticated user.
     */
    public function user(): JsonResponse
    {
        return $this->successResponse(Auth::user(), 'User retrieved successfully');
    }
}
