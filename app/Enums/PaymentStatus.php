<?php

namespace App\Enums;

use App\Enums\Concerns\BaseEnum;

enum PaymentStatus: int
{
    use BaseEnum;

    case PENDING = 1;
    case COMPLETED = 2;
    case FAILED = 3;
    case REFUNDED = 4;

    /**
     * Meta properties.
     */
    public static function metaProperties(): array
    {
        return [
            self::PENDING() => [
                'title' => 'Pending',
            ],
            self::COMPLETED() => [
                'title' => 'Completed',
            ],
            self::FAILED() => [
                'title' => 'Failed',
            ],
            self::REFUNDED() => [
                'title' => 'Refunded',
            ],
        ];
    }
}
