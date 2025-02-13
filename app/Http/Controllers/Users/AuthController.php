<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Enums\Roles\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\Models\Roles\Role;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): UserResource
    {
        $clientRole = Role::where(Role::NAME, RoleEnum::CLIENT->value)->first();

        $user = User::create([
            User::NAME => $request->getName(),
            User::EMAIL => $request->getEmail(),
            User::PASSWORD => Hash::make($request->getPassword()),
            User::ROLE_ID => $clientRole->getId(),
            User::PHONE => $request->getPhone() ? $request->getPhone() : null,
        ]);

        return new UserResource($user);
    }

    public function login(LoginRequest $request): UserResource|JsonResponse
    {
        $user = User::where(User::EMAIL, $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->{User::PASSWORD})) {
            return response()->json([
                'message' => 'Nepareiza informācija'
            ], 401);
        }

        $token = $user->createToken($user->getName());

        return (new UserResource($user))->withToken($token->plainTextToken);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Tu esi izlogojies'
        ]);
    }
}
