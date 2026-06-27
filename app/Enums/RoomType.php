<?php

namespace App\Enums;

use App\Enums\Concerns\BaseEnum;

enum RoomType: int
{
    use BaseEnum;

    case SINGLE = 1;
    case DOUBLE = 2;
    case SUITE = 3;
    case DELUXE = 4;

    /**
     * Meta properties.
     */
    public static function metaProperties(): array
    {
        return [
            self::SINGLE() => [
                'title' => 'Single',
                'color' => 'blue',
            ],
            self::DOUBLE() => [
                'title' => 'Double',
                'color' => 'green',
            ],
            self::SUITE() => [
                'title' => 'Suite',
                'color' => 'purple',
            ],
            self::DELUXE() => [
                'title' => 'Deluxe',
                'color' => 'amber',
            ],
        ];
    }
}
