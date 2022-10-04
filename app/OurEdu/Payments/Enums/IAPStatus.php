<?php

namespace App\OurEdu\Payments\Enums;

abstract class IAPStatus
{
    private const STATUSES = [
        0 => 'Success',
        21000 => 'The request to the App Store was not made using the HTTP POST request method.',
        21002 => 'The data in the receipt-data property was malformed or the service experienced a temporary issue. Try again.',
        21003 => 'The receipt could not be authenticated.',
        21004 => 'The shared secret you provided does not match the shared secret on file for your account.',
        21005 => 'The receipt server was temporarily unable to provide the receipt. Try again.',
        21007 => 'This receipt is from the test environment, but it was sent to the production environment for verification.',
        21008 => 'This receipt is from the production environment, but it was sent to the test environment for verification.',
        21009 => 'Internal data access error. Try again later.',
        21010 => 'The user account cannot be found or has been deleted.',
    ];

    public static function isValid($status): bool
    {
        return $status == 0;
    }

    public static function get($status): string
    {
        return self::STATUSES[$status] ?? 'Status not found';
    }
}
