<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Auth
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * Exchange credentials for a bearer token.
     *
     * @unauthenticated
     *
     * @response 200 {"token": "1|abc...", "user": {"id": 1, "name": "Jane", "email": "jane@example.com"}}
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            throw ValidationException::withMessages(['email' => [trans('auth.failed')]]);
        }

        return response()->json([
            'token' => $user->createToken($request->string('device_name', 'api')->toString())->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Current user
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Logout
     *
     * Revoke the current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()->delete();

        return response()->json(status: 204);
    }
}
