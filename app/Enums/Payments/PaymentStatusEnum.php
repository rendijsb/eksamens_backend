<?php

declare(strict_types=1);

namespace App\Enums\Payments;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
