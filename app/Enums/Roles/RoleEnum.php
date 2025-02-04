<?php

declare(strict_types=1);

namespace App\Enums\Roles;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case CLIENT = 'client';
}
