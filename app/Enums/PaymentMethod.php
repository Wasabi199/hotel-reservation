<?php

namespace App\Enums;

use App\Enums\Concerns\BaseEnum;

enum PaymentMethod: string
{
    use BaseEnum;

    case CARD = 'card';
    case CASH = 'cash';
    case PAYPAL = 'paypal';

    /**
     * Meta properties.
     */
    public static function metaProperties(): array
    {
        return [
            self::CARD() => [
                'title' => 'Card',
            ],
            self::CASH() => [
                'title' => 'Cash',
            ],
            self::PAYPAL() => [
                'title' => 'Paypal',
            ],
        ];
    }
}
