<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Enums\Images\ImageTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\ChangeProfilePasswordRequest;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\DeleteUserRequest;
use App\Http\Requests\Users\EditUserRequest;
use App\Http\Requests\Users\GetAllUsersRequest;
use App\Http\Requests\Users\GetUserByIdRequest;
use App\Http\Requests\Users\UpdateProfileImageRequest;
use App\Http\Requests\Users\UpdateUserProfileRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Auth\UserResourceCollection;
use App\Models\Users\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function getAll(GetAllUsersRequest $request): UserResourceCollection
    {
        $query = User::query()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*')
            ->where('users.id', '!=', $request->user()->id);

        if ($request->getSearch()) {
            $searchTerm = $request->getSearch();

            $roleMappings = [
                'administrators' => 1,
                'moderators' => 2,
                'klients' => 3
            ];

            $matchedRoleIds = [];
            foreach ($roleMappings as $roleName => $roleId) {
                if (stripos($roleName, $searchTerm) !== false) {
                    $matchedRoleIds[] = $roleId;
                }
            }

            $query->where(function($q) use ($searchTerm, $matchedRoleIds) {
                $q->where('users.'.User::NAME, 'like', "%{$searchTerm}%")
                    ->orWhere('users.'.User::EMAIL, 'like', "%{$searchTerm}%")
                    ->orWhere('users.'.User::PHONE, 'like', "%{$searchTerm}%");

                if (!empty($matchedRoleIds)) {
                    $q->orWhereIn('users.'.User::ROLE_ID, $matchedRoleIds);
                }
            });
        }

        $query->with('relatedRole');

        $sortField = $request->getSortBy();
        $sortDirection = $request->getSortDir();

        if ($sortField === 'role_name') {
            $query->orderBy('roles.name', $sortDirection);
        } else {
            $query->orderBy('users.' . $sortField, $sortDirection);
        }

        $users = $query->paginate(10);

        return new UserResourceCollection($users);
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

    public function getUserProfile(): UserResource
    {
        return new UserResource(auth()->user());
    }

    public function updateUserProfile(UpdateUserProfileRequest $request): UserResource
    {
        $user = auth()->user();

        $user->update([
            User::NAME => $request->getName(),
            User::EMAIL => $request->getEmail(),
            User::PHONE => $request->getPhone(),
        ]);

        return  new UserResource($user);
    }

    public function changePassword(ChangeProfilePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();

        $user->update([
            User::PASSWORD => Hash::make($request->getNewPassword()),
        ]);

        return response()->json(Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws FileNotFoundException
     */
    public function updateProfileImage(UpdateProfileImageRequest $request): UserResource
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->getProfileImage()) {
            $oldPath = ImageTypeEnum::PROFILE->value . '/' . $user->getProfileImage();
            Storage::disk('s3')->delete($oldPath);
        }

        $imageName = Str::uuid() . '.' . $request->getProfileImage()->getClientOriginalExtension();
        $path = ImageTypeEnum::PROFILE->value . '/' . $imageName;

        Storage::disk('s3')->put($path, $request->getProfileImage()->get());

        $user->update([
            User::PROFILE_IMAGE => $imageName
        ]);

        $freshUser = $user->fresh();

        return new UserResource($freshUser);
    }

    public function removeProfileImage(): UserResource|JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->getProfileImage()) {
            $path = ImageTypeEnum::PROFILE->value . '/' . $user->getProfileImage();
            Storage::disk('s3')->delete($path);

            $user->update([
                User::PROFILE_IMAGE => null
            ]);
        }

        return new UserResource($user->fresh());
    }
}
