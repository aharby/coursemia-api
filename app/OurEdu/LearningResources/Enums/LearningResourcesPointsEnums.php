<?php

namespace App\OurEdu\LearningResources\Enums;

use BenSampo\Enum\Enum;

final class LearningResourcesPointsEnums extends Enum
{
    const VIDEO = 100;
    const AUDIO = 80;
    const FLASH = 40;
    const PDF = 50;
    const PAGE = 50;

    public static function getLearningResourcesPointsEnums()
    {
        return [
            LearningResourcesEnums::Video => self::VIDEO,
            LearningResourcesEnums::Audio => self::AUDIO,
            LearningResourcesEnums::FLASH => self::FLASH,
            LearningResourcesEnums::PDF => self::PDF,
            LearningResourcesEnums::PAGE => self::PAGE,
        ];
    }
}

