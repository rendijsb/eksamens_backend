<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAll(Request $request): array
    {
//        if ($request->user()->role !== 'admin') {
//            return [
//                'message' => 'Nav atļaujas',
//            ];
//        }

        return [
            'users' => User::all(),
        ];
    }
}
