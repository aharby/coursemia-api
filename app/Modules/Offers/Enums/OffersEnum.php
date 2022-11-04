<?php

namespace App\Modules\Offers\Enums;

class OffersEnum
{
    public const OFFER_TYPE_PERCENTAGE = "Percentage",
        OFFER_TYPE_VALUE = 'Value';

    public static array $offersTypes = [
        1 => self::OFFER_TYPE_PERCENTAGE,
        2 => self::OFFER_TYPE_VALUE,
    ];

    public static function getOfferType($offerType)
    {
        return self::$offersTypes[$offerType];
    }

}
