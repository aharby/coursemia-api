<?php


namespace App\OurEdu\GeneralQuizzes\Enums;


abstract class QuestionsTypesEnums
{
    const MULTI_CHOICE = 'multiple_choice';
    const TRUE_FALSE = 'true_false';
    const SINGLE_CHOICE = 'single_choice';
    const TRUE_FALSE_WITH_CORRECT = 'true_false_with_correct';
    const ESSAY = 'essay';
    const DRAG_DROP_TEXT = 'drag_drop_text';
    const DRAG_DROP_IMAGE = 'drag_drop_image';
    const COMPLETE = 'complete';

    public static function list()
    {
        return [
          self::MULTI_CHOICE => trans("app.multiple_choice"),
          self::TRUE_FALSE => trans("app.true_false"),
          self::SINGLE_CHOICE => trans("app.single_choice"),
          self::TRUE_FALSE_WITH_CORRECT => trans("app.true_false_with_correct"),
          self::ESSAY => trans("app.essay"),
          self::DRAG_DROP_TEXT => trans("app.drag_drop_text"),
          self::DRAG_DROP_IMAGE => trans("app.drag_drop_image"),
          self::COMPLETE =>trans('app.complete'),
        ];
    }

    public static function getLabel(string $key)
    {
        return array_key_exists($key, self::list()) ? self::list()[$key] : "";
    }

    public static function formativeTestList()
    {
        return [
          self::MULTI_CHOICE => trans("app.multiple_choice"),
          self::TRUE_FALSE => trans("app.true_false"),
          self::SINGLE_CHOICE => trans("app.single_choice"),
          self::TRUE_FALSE_WITH_CORRECT => trans("app.true_false_with_correct"),
          self::DRAG_DROP_TEXT => trans("app.drag_drop_text"),
          self::DRAG_DROP_IMAGE => trans("app.drag_drop_image"),
          self::COMPLETE =>trans('app.complete'),
        ];
    }

}
