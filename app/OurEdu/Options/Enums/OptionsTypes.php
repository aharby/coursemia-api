<?php

namespace App\OurEdu\Options\Enums;

abstract class OptionsTypes
{
    const ACADEMIC_YEAR = 'academic_year',
        EDUCATIONAL_TERM = 'educational_term',
        RESOURCE_DIFFICULTY_LEVEL = 'resource_difficulty_level',
        RESOURCE_LEARNING_OUTCOME = 'resource_learning_outcome',
        TRUE_FALSE_TRUE_FALSE_TYPE = 'true_false_true_false_type',
        MULTI_CHOICE_MULTIPLE_CHOICE_TYPE = 'multiple_choice_multiple_choice_type',
        MATCHING_TYPE = 'matching_type',
        MULTI_MATCHING_TYPE = 'multiple_matching_type',
        VIDEO_VIDEO_TYPE = 'video_video_type',
        AUDIO_AUDIO_TYPE = 'audio_audio_type',
        DRAG_DROP_DRAG_DROP_TYPE = 'drag_drop_drag_drop_type',
        PDF_PDF_TYPE = 'pdf_pdf_type',
        PICTURE_PICTURE_TYPE = 'picture_picture_type',
        ESSAY_TYPE = 'essay_type',
        STAR_RATING = 'star_rating',
        SCALE_RATING = 'scale_rating',
        COMPLETE_TYPE = 'complete_type',
        HOTSPOT_TYPE = 'hotspot_type';


    public static function getOptionsTypes()
    {
        return [
            self::ACADEMIC_YEAR => self::ACADEMIC_YEAR,
            self::EDUCATIONAL_TERM => self::EDUCATIONAL_TERM
        ];
    }

}
