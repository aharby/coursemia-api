<?php

namespace App\OurEdu\Payments\Enums;

abstract class PaymentMethodsEnum
{
    public const VISA = 'visa',
        WALLET = 'wallet';


    public static function getPaymentMethods()
    {
        return [
            self::VISA => trans('payments.visa'),
            self::WALLET => trans('payments.wallet')
        ];
    }
}
