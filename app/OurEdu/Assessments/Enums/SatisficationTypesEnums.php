<?php


namespace App\OurEdu\Assessments\Enums;


abstract class SatisficationTypesEnums
{
    const EXCELLENT = 'excellent';
    const VERY_GOOD = 'very_good';
    const GOOD = 'good';
    const BAD = 'bad';
    const VERY_BAD = 'very_bad';


    public static function list()
    {
        return [
            self::EXCELLENT => trans("app.excellent"),
            self::VERY_GOOD => trans("app.very_good"),
            self::GOOD =>trans('app.good'),
            self::BAD=>trans('app.bad'),
            self::VERY_BAD => trans('app.very_bad'),

        ];
    }


    public static function satisficationTypes()
    {
        return [
            self::EXCELLENT,
            self::VERY_GOOD,
            self::GOOD,
            self::BAD,
            self::VERY_BAD
        ];
    }

}
