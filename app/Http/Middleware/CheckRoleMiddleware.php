<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckRoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): mixed
    {
        $user = $request->user();

        if (!$user) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }

        $user->load('relatedRole');

        $allowedRoles = explode('|', $roles);

        if (!$user->relatedRole || !in_array($user->relatedRole->getName(), $allowedRoles)) {
            return new JsonResponse(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
