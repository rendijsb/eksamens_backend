<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResourceCollection;
use App\Models\Users\User;

class UserController extends Controller
{
    public function getAll(): UserResourceCollection
    {
        return new UserResourceCollection(User::paginate(15));
    }
}
