<?php

namespace App\OurEdu\LearningResources\Enums;

abstract class LearningResourcesEnums
{
    const MULTI_CHOICE = 'multiple_choice';
    const TRUE_FALSE = 'true_false';
    const Video = 'video';
    const Audio = 'audio';
    const FLASH = 'flash';
    const DRAG_DROP = 'drag_drop';
    const PDF = 'pdf';
    const PICTURE = 'picture';
    const MATCHING = 'matching';
    const MULTIPLE_MATCHING = 'multiple_matching';
    const PAGE = 'page';
    const COMPLETE = 'complete';
    const HOTSPOT = 'hotspot';



    const  LearningResources = [
        self::TRUE_FALSE => self::TRUE_FALSE_ACCEPT_CRITERIA,
        self::MULTI_CHOICE => self::MULTI_CHOICE_ACCEPT_CRITERIA,
        self::Video => self::Video_ACCEPT_CRITERIA,
        self::Audio => self::Audio_ACCEPT_CRITERIA,
        self::FLASH => self::FLASH_ACCEPT_CRITERIA,
        self::DRAG_DROP => self::DRAG_DROP_ACCEPT_CRITERIA,
        self::PDF => self::PDF_ACCEPT_CRITERIA,
        self::PICTURE => self::PICTURE_ACCEPT_CRITERIA,
        self::MATCHING => self::MATCHING_ACCEPT_CRITERIA,
        self::MULTIPLE_MATCHING => self::MULTIPLE_MATCHING_ACCEPT_CRITERIA,
        self::PAGE => self::PAGE_ACCEPT_CRITERIA,
        self::COMPLETE => self::COMPLETE_ACCEPT_CRITERIA,
        self::HOTSPOT => self::HOTSPOT_ACCEPT_CRITERIA,
    ];

    public static function getQuestionLearningResources()
    {
        return [
            self::TRUE_FALSE => self::TRUE_FALSE,
            self::MULTI_CHOICE => self::MULTI_CHOICE,
            self::COMPLETE => self::COMPLETE,
            self::DRAG_DROP => self::DRAG_DROP,
            self::MATCHING => self::MATCHING,
            self::MULTIPLE_MATCHING => self::MULTIPLE_MATCHING,
            self::HOTSPOT => self::HOTSPOT,
        ];
    }

    public static function getNotQuestionResources()
    {
        return [
            self::Video => self::Video,
            self::Audio => self::Audio,
            self::FLASH => self::FLASH,
            self::PDF => self::PDF,
            self::PICTURE => self::PICTURE,
            self::PAGE => self::PAGE,
        ];
    }

    public static function getAcceptanceCriteria($type)
    {
        return self::LearningResources[$type];
    }

    const COMPLETE_ACCEPT_CRITERIA = [

        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'number_of_question' => [
            'initial_value' => 1,
            'data_type' => 'number',
            'validation' => 'required|numeric|min:1|max:10'
        ],
        'difficulty_level' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
        'learning_outcome' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ]
    ];

    const TRUE_FALSE_ACCEPT_CRITERIA = [

        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'number_of_question' => [
            'initial_value' => 1,
            'data_type' => 'number',
            'validation' => 'required|numeric|min:1|max:10'
        ],
        'difficulty_level' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
        'learning_outcome' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
        'true_false_type' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
    ];

    const MULTI_CHOICE_ACCEPT_CRITERIA = [

        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'number_of_question' => [
            'initial_value' => 1,
            'data_type' => 'number',
            'validation' => 'required|numeric|min:1|max:10'
        ],
        'difficulty_level' => [
            'validation' => 'required',
            'data_type' => 'drop_down',

            'have_options' => true,

        ],
        'learning_outcome' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,

        ],
        'multiple_choice_type' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
    ];

    const Video_ACCEPT_CRITERIA = [
        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'video_type' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,

        ],
    ];

    const Audio_ACCEPT_CRITERIA = [
        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'audio_type' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
    ];

    const FLASH_ACCEPT_CRITERIA = [
        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => 'required',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
//        'flash' => [
//            'validation' => 'required',
//            'data_type' => 'upload',
//            'have_options' => false,
//        ],
    ];

    const DRAG_DROP_ACCEPT_CRITERIA = [

        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'number_of_question' => [
            'initial_value' => 1,
            'data_type' => 'number',
            'validation' => 'required|numeric|min:1|max:10'
        ],
        'difficulty_level' => [
            'validation' => 'required',
            'data_type' => 'drop_down',

            'have_options' => true,

        ],
        'learning_outcome' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,

        ],
        'drag_drop_type' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
    ];

    const PDF_ACCEPT_CRITERIA = [
        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => '',
            'data_type' => 'text_area',
        ],
        'pdf_type' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,
        ],
    ];

    const PICTURE_ACCEPT_CRITERIA = [
        'due_date' => [
            'validation' => 'required|numeric|min:1',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => '',
            'data_type' => 'text_area',
        ],

    ];


    const MATCHING_ACCEPT_CRITERIA = [
        'due_date' => [
            'validation' => 'numeric|min:1|required',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'number_of_question' => [
            'initial_value' => 1,
            'data_type' => 'number',
            'validation' => 'numeric|min:1|max:10|required'
        ],
        'difficulty_level' => [
            'validation' => 'required',
            'data_type' => 'drop_down',

            'have_options' => true,

        ],
        'learning_outcome' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,

        ],
    ];

    const MULTIPLE_MATCHING_ACCEPT_CRITERIA = [

        'due_date' => [
            'validation' => 'numeric|min:1|required',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'number_of_question' => [
            'initial_value' => 1,
            'data_type' => 'number',
            'validation' => 'numeric|min:1|max:10|required'
        ],
        'difficulty_level' => [
            'validation' => 'required',
            'data_type' => 'drop_down',

            'have_options' => true,

        ],
        'learning_outcome' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,

        ],

    ];

    const PAGE_ACCEPT_CRITERIA = [

        'due_date' => [
            'validation' => 'numeric|min:1|required',
            'initial_value' => 1,
            'data_type' => 'number',
        ],
        'description' => [
            'validation' => 'required',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],


    ];

    const HOTSPOT_ACCEPT_CRITERIA = [
        'description' => [
            'validation' => '',
            'initial_value' => "",
            'data_type' => 'text_area',
        ],
        'number_of_question' => [
            'initial_value' => 1,
            'data_type' => 'number',
            'validation' => 'numeric|min:1|max:10|required'
        ],
        'difficulty_level' => [
            'validation' => 'required',
            'data_type' => 'drop_down',

            'have_options' => true,

        ],
        'learning_outcome' => [
            'validation' => 'required',
            'data_type' => 'drop_down',
            'have_options' => true,

        ],

    ];
    const KEY_OPTIONS = [
        'difficulty_level',
        'learning_outcome',
        'pdf_type',
        'drag_drop_type',
        'audio_type',
        'video_type',
        'multiple_choice_type',
        'true_false_type',
    ];
}
