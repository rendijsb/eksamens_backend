<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\DeleteUserRequest;
use App\Http\Requests\Users\EditUserRequest;
use App\Http\Requests\Users\GetUserByIdRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Auth\UserResourceCollection;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAll(Request $request): UserResourceCollection
    {
        return new UserResourceCollection(
            User::where('id', '!=', $request->user()->id)
                ->paginate(15)
        );
    }

    public function deleteUser(DeleteUserRequest $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        if ($user->getId() === $request->user()->getId()) {
            return new JsonResponse(['message' => 'Cannot delete your own account'], 403);
        }

        $user->delete();

        return new JsonResponse([], 204);
    }

    public function getUserById(GetUserByIdRequest $request): UserResource
    {
        $user = User::findOrFail($request->getUserId());

        return new UserResource($user);
    }

    public function createUser(CreateUserRequest $request): UserResource
    {
        $user = User::create([
            User::NAME => $request->getName(),
            User::EMAIL => $request->getEmail(),
            User::PHONE => $request->getPhone(),
            User::ROLE_ID => $request->getRole(),
            User::PASSWORD => Hash::make($request->getPassword()),
        ]);

        return new UserResource($user);
    }

    public function editUser(EditUserRequest $request): UserResource
    {
        $user = User::findOrFail($request->getUserId());

        $updateData = [
            User::NAME => $request->getName(),
            User::EMAIL => $request->getEmail(),
            User::PHONE => $request->getPhone(),
            User::ROLE_ID => $request->getRole(),
        ];

        if ($request->getPassword()) {
            $updateData[User::PASSWORD] = Hash::make($request->getPassword());
        }

        $user->update($updateData);

        return new UserResource($user);
    }
}
