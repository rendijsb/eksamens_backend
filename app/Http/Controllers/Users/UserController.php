<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\DeleteUserRequest;
use App\Http\Resources\Auth\UserResourceCollection;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAll(Request $request): UserResourceCollection
    {
        return new UserResourceCollection(
            User::where('id', '!=', $request->user()->id)
                ->paginate(15)
        );
    }

    public function delete(DeleteUserRequest $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        if ($user->id === $request->user()->id) {
            return new JsonResponse(['message' => 'Cannot delete your own account'], 403);
        }

        $user->delete();

        return new JsonResponse([], 204);
    }
}
