<?php

namespace App\Enums;

use App\Enums\Concerns\BaseEnum;

enum ReservationStatus: int
{
    use BaseEnum;
    case PENDING = 1;
    case CONFIRMED = 2;
    case CANCELLED = 3;
    case COMPLETED = 4;

    /**
     * Meta properties.
     */
    public static function metaProperties(): array
    {
        return [
            self::PENDING() => [
                'title' => 'Pending',
                'color' => 'yellow',
            ],
            self::CONFIRMED() => [
                'title' => 'Confirmed',
                'color' => 'green',
            ],
            self::CANCELLED() => [
                'title' => 'Cancelled',
                'color' => 'red',
            ],
            self::COMPLETED() => [
                'title' => 'Completed',
                'color' => 'green',
            ],
        ];
    }
}
