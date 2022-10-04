<?php

namespace App\OurEdu\Payments\Enums;

abstract class TransactionTypesEnums
{
    public const REFUND = 'refund',
        DEPOSIT = 'deposit',
        WITHDRAWAL = 'withdrawal';

    public static function getTransactionTypes()
    {
        return [
            self::DEPOSIT => trans('payments.'.self::DEPOSIT),
            self::WITHDRAWAL => trans('payments.'.self::WITHDRAWAL),
            self::REFUND => trans('payments.'.self::REFUND),
        ];
    }
}
