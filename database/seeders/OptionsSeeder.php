<?php

namespace Database\Seeders;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\Options\Option;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        DB::table('option_translations')->delete();
        DB::table('options')->delete();

        Schema::enableForeignKeyConstraints();

        $rows = [];
        //        $rows[] = [
        //            'title:en' => '2019 / 2020',
        //            'title:ar' => '2019 / 2020',
        //            'is_active' => 1,
        //            'slug' => OptionsTypes::ACADEMIC_YEAR,
        //            'type' => OptionsTypes::ACADEMIC_YEAR,
        //        ];
        //        $rows[] = [
        //            'title:en' => '2020 / 2021',
        //            'title:ar' => '2020 / 2021',
        //            'is_active' => 1,
        //            'slug' => OptionsTypes::ACADEMIC_YEAR,
        //            'type' => OptionsTypes::ACADEMIC_YEAR,
        //        ];
        //        $rows[] = [
        //            'title:en' => '2021 / 2022',
        //            'title:ar' => '2021 / 2022',
        //            'is_active' => 1,
        //            'slug' => OptionsTypes::ACADEMIC_YEAR,
        //            'type' => OptionsTypes::ACADEMIC_YEAR,
        //        ];
        //        $rows[] = [
        //            'title:en' => 'educational term',
        //            'title:ar' => 'educational term',
        //            'is_active' => 1,
        //            'slug' => OptionsTypes::EDUCATIONAL_TERM,
        //            'type' => OptionsTypes::EDUCATIONAL_TERM,
        //        ];
        //        $rows[] = [
        //            'title:en' => '2022 / 2023',
        //            'title:ar' => '2022 / 2023',
        //            'is_active' => 1,
        //            'slug' => OptionsTypes::ACADEMIC_YEAR,
        //            'type' => OptionsTypes::ACADEMIC_YEAR,
        //        ];
        //        $rows[] = [
        //            'title:en' => '2023 / 2024',
        //            'title:ar' => '2023 / 2024',
        //            'is_active' => 1,
        //            'slug' => OptionsTypes::ACADEMIC_YEAR,
        //            'type' => OptionsTypes::ACADEMIC_YEAR,
        //        ];
        //        $rows[] = [
        //            'title:en' => '2024 / 2025',
        //            'title:ar' => '2024 / 2025',
        //            'is_active' => 1,
        //            'slug' => OptionsTypes::ACADEMIC_YEAR,
        //            'type' => OptionsTypes::ACADEMIC_YEAR,
        //        ];


        $rows[] = [
            'title:en' => 'one choice',
            'title:ar' => 'اختيار واحد',
            'is_active' => 1,
            'type' => OptionsTypes::MULTI_CHOICE_MULTIPLE_CHOICE_TYPE,
            'slug' => ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_SINGLE_CHOICE,
        ];

        $rows[] = [
            'title:en' => 'Multiple choice',
            'title:ar' => 'اختيار متعدد',
            'is_active' => 1,
            'type' => OptionsTypes::MULTI_CHOICE_MULTIPLE_CHOICE_TYPE,
            'slug' => ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE,
        ];


        $rows[] = [
            'title:en' => 'Easy',
            'title:ar' => 'سهل',
            'is_active' => 1,
            'type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL,
            'slug' => ResourceOptionsSlugEnum::EASY,

        ];

        $rows[] = [
            'title:en' => 'Medium',
            'title:ar' => 'متوسط',
            'is_active' => 1,
            'type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL,
            'slug' => ResourceOptionsSlugEnum::MEDIUM,

        ];

        $rows[] = [
            'title:en' => 'Difficult',
            'title:ar' => 'صعب',
            'is_active' => 1,
            'type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL,
            'slug' => ResourceOptionsSlugEnum::DIFFICULT,

        ];

        $rows[] = [
            'title:en' => 'Understand',
            'title:ar' => 'القهم',
            'is_active' => 1,
            'type' => OptionsTypes::RESOURCE_LEARNING_OUTCOME,
            'slug' => ResourceOptionsSlugEnum::UNDERSTAND,
        ];
        $rows[] = [
            'title:en' => 'Remembering',
            'title:ar' => 'التذكر',
            'is_active' => 1,
            'type' => OptionsTypes::RESOURCE_LEARNING_OUTCOME,
            'slug' => ResourceOptionsSlugEnum::REMEMBERING,

        ];
        $rows[] = [
            'title:en' => 'Enforcement',
            'title:ar' => 'Enforcement',
            'is_active' => 1,
            'type' => OptionsTypes::RESOURCE_LEARNING_OUTCOME,
            'slug' => ResourceOptionsSlugEnum::ENFORCEMENT,

        ];
        $rows[] = [
            'title:en' => 'High Skills Thinking',
            'title:ar' => 'مهارات التفكير العليا',
            'is_active' => 1,
            'type' => OptionsTypes::RESOURCE_LEARNING_OUTCOME,
            'slug' => ResourceOptionsSlugEnum::HIGH_SKILLS_THINKING,

        ];
        $rows[] = [
            'title:en' => 'True or false',
            'title:ar' => 'True or false',
            'is_active' => 1,
            'type' => OptionsTypes::TRUE_FALSE_TRUE_FALSE_TYPE,
            'slug' => ResourceOptionsSlugEnum::TRUE_FALSE,

        ];
        $rows[] = [
            'title:en' => 'true or false with correct',
            'title:ar' => 'true or false with correct',
            'is_active' => 1,
            'type' => OptionsTypes::TRUE_FALSE_TRUE_FALSE_TYPE,
            'slug' => ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT,

        ];
        $rows[] = [
            'title:en' => 'link',
            'title:ar' => 'link',
            'is_active' => 1,
            'type' => OptionsTypes::VIDEO_VIDEO_TYPE,
            'slug' => ResourceOptionsSlugEnum::LINK,
        ];
        $rows[] = [
            'title:en' => 'upload',
            'title:ar' => 'upload',
            'is_active' => 1,
            'type' => OptionsTypes::VIDEO_VIDEO_TYPE,
            'slug' => ResourceOptionsSlugEnum::UPLOAD,
        ];

        $rows[] = [
            'title:en' => 'link',
            'title:ar' => 'link',
            'is_active' => 1,
            'type' => OptionsTypes::AUDIO_AUDIO_TYPE,
            'slug' => ResourceOptionsSlugEnum::LINK,

        ];
        $rows[] = [
            'title:en' => 'upload',
            'title:ar' => 'upload',
            'is_active' => 1,
            'type' => OptionsTypes::AUDIO_AUDIO_TYPE,
            'slug' => ResourceOptionsSlugEnum::UPLOAD,

        ];


        $rows[] = [
            'title:en' => 'Text',
            'title:ar' => 'Text',
            'is_active' => 1,
            'type' => OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE,
            'slug' => ResourceOptionsSlugEnum::TEXT
        ];
        $rows[] = [
            'title:en' => 'Image',
            'title:ar' => 'Image',
            'is_active' => 1,
            'type' => OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE,
            'slug' => ResourceOptionsSlugEnum::IMAGE
        ];
        $rows[] = [
            'title:en' => 'link',
            'title:ar' => 'link',
            'is_active' => 1,
            'type' => OptionsTypes::PDF_PDF_TYPE,
            'slug' => ResourceOptionsSlugEnum::LINK,

        ];
        $rows[] = [
            'title:en' => 'upload',
            'title:ar' => 'upload',
            'is_active' => 1,
            'type' => OptionsTypes::PDF_PDF_TYPE,
            'slug' => ResourceOptionsSlugEnum::UPLOAD,

        ];
        $rows[] = [
            'title:en' => 'link',
            'title:ar' => 'link',
            'is_active' => 1,
            'type' => OptionsTypes::PICTURE_PICTURE_TYPE,
            'slug' => ResourceOptionsSlugEnum::LINK,

        ];
        $rows[] = [
            'title:en' => 'upload',
            'title:ar' => 'upload',
            'is_active' => 1,
            'type' => OptionsTypes::PICTURE_PICTURE_TYPE,
            'slug' => ResourceOptionsSlugEnum::UPLOAD,

        ];

        $rows[] = [
            'title:en' => 'Star scale rating',
            'title:ar' => trans('app.star_rating'),
            'is_active' => 1,
            'type' => OptionsTypes::STAR_RATING,
            'slug' => OptionsTypes::STAR_RATING,
        ];

        $rows[] = [
            'title:en' => 'Scale rating',
            'title:ar' => trans('app.scale_rating'),
            'is_active' => 1,
            'type' => OptionsTypes::SCALE_RATING,
            'slug' => OptionsTypes::SCALE_RATING,
        ];


        foreach ($rows as $row) {
            Option::create($row);
        }
    }
}
