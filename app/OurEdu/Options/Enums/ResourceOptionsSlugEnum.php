<?php

namespace App\OurEdu\Options\Enums;

use App\OurEdu\LearningResources\Enums\DifficultlyLevelEnums;

abstract class ResourceOptionsSlugEnum
{
    const MULTIPLE_CHOICE_SLUG_SINGLE_CHOICE = 'single_choice',
        MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE = 'multiple_choice',
        LINK = 'link',
        UPLOAD = 'upload',
        IMAGE = 'image',
        TEXT = 'text',

        EASY = DifficultlyLevelEnums::EASY,
        MEDIUM =  DifficultlyLevelEnums::MEDIUM,
        DIFFICULT =  DifficultlyLevelEnums::DIFFICULT,

        UNDERSTAND = 'understand',
        REMEMBERING = 'remembering',
        ENFORCEMENT = 'enforcement',
        HIGH_SKILLS_THINKING = 'high_skills_thinking',
        TRUE_FALSE = 'true_false',
        TRUE_FALSE_WITH_CORRECT = 'true_false_with_correct',
        ESSAY = 'essay';
}
