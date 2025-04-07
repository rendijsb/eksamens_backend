<?php

declare(strict_types=1);

namespace App\Enums\Orders;

enum OrderStatusEnum: string
{
    case STATUS_PENDING = 'pending';
    case STATUS_PROCESSING = 'processing';
    case STATUS_COMPLETED = 'completed';
    case STATUS_CANCELLED = 'cancelled';
    case STATUS_FAILED = 'failed';
}
