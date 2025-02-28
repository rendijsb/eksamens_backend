<?php

declare(strict_types=1);

namespace App\Enums\Products;

enum ProductEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
