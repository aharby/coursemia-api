<?php


namespace App\OurEdu\Assessments\Enums;


abstract class QuestionTypesEnums
{
    const MULTI_CHOICE = 'multiple_choice';
    const SINGLE_CHOICE = 'single_choice';
    const SCALE_RATING = 'scale_rating';
    const STAR_RATING = 'star_rating';
    const MATRIX = 'matrix';
    const SATISFICATION_RATING = 'satisfication_rating';
    const ESSAY_QUESTION = 'essay_question';


    public static function list()
    {
        return [
          self::MULTI_CHOICE => trans("app.multiple_choice"),
          self::SINGLE_CHOICE => trans("app.single_choice"),
          self::STAR_RATING =>trans('app.star_rating'),
          self::SCALE_RATING=>trans('app.scale_rating'),
          self::MATRIX => trans('app.matrix'),
          self::SATISFICATION_RATING => trans('app.satisfication_rating'),
          self::ESSAY_QUESTION => trans('app.essay_question'),

        ];
    }


    public static function questionTypes()
    {
        return [
            self::MULTI_CHOICE,
            self::SINGLE_CHOICE,
            self::SCALE_RATING,
            self::STAR_RATING,
            self::MATRIX,
            self::SATISFICATION_RATING,
            self::ESSAY_QUESTION,
        ];
    }
    public static function getLabel(string $key)
    {
        return array_key_exists($key, self::list()) ? self::list()[$key] : "";
    }
}
