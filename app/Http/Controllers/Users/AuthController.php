<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Enums\Roles\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Auth\UserResource;
use App\Mail\PasswordReset;
use App\Mail\WelcomeUser;
use App\Models\Roles\Role;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): UserResource|JsonResponse
    {
        $clientRole = Role::where(Role::NAME, RoleEnum::CLIENT->value)->first();

        /** @var User $user */
        $user = User::create([
            User::NAME => $request->getName(),
            User::EMAIL => $request->getEmail(),
            User::PASSWORD => Hash::make($request->getPassword()),
            User::ROLE_ID => $clientRole->getId(),
            User::PHONE => $request->getPhone() ? $request->getPhone() : null,
        ]);

        $user->getOrCreateNotificationPreferences();

        Mail::to($user->getEmail())->send(new WelcomeUser($user));

        return new UserResource($user);
    }

    public function login(LoginRequest $request): UserResource|JsonResponse
    {
        $user = User::where(User::EMAIL, $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->{User::PASSWORD})) {
            return response()->json([
                'message' => 'Nepareiza informācija'
            ], 422);
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

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where(User::EMAIL, $request->getEmail())->first();

        if (!$user) {
            return response()->json([
                'message' => 'Ja e-pasta adrese eksistē mūsu sistēmā, jūs saņemsiet paroles atiestatīšanas instrukcijas.'
            ], 200);
        }

        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->getEmail()],
            [
                'email' => $user->getEmail(),
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        Mail::to($user->getEmail())->send(new PasswordReset($user, $token));

        return response()->json([
            'message' => 'Ja e-pasta adrese eksistē mūsu sistēmā, jūs saņemsiet paroles atiestatīšanas instrukcijas.'
        ], 200);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->getEmail())
            ->first();

        if (!$passwordReset || !Hash::check($request->getToken(), $passwordReset->token)) {
            return response()->json([
                'message' => 'Nederīgs atiestatīšanas tokens.'
            ], 422);
        }

        $tokenCreatedAt = Carbon::parse($passwordReset->created_at);
        if ($tokenCreatedAt->addMinutes(60)->isPast()) {
            return response()->json([
                'message' => 'Atiestatīšanas tokens ir beidzies. Lūdzu, pieprasiet jaunu.'
            ], 422);
        }

        $user = User::where(User::EMAIL, $request->getEmail())->first();

        if (!$user) {
            return response()->json([
                'message' => 'Lietotājs nav atrasts.'
            ], 422);
        }

        $user->update([
            User::PASSWORD => Hash::make($request->getPassword())
        ]);

        DB::table('password_resets')->where('email', $request->getEmail())->delete();

        return response()->json([
            'message' => 'Parole sekmīgi atiestatīta. Tagad varat ielogoties ar jauno paroli.'
        ], 200);
    }
}
