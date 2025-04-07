<?php

declare(strict_types=1);

namespace App\Enums\Payments;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
