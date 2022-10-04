<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;

class ResourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Resource::latest()->forceDelete();

        $data = [
            [
                'id' => 1,
                'title:en' => 'True & false',
                'title:ar' => 'صح و خطأ',
                'description:en' => 'true_false',
                'description:ar' => 'صح و خطأ',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::TRUE_FALSE,
            ],
            [
                'id' => 2,
                'title:en' => 'Video',
                'title:ar' => 'مرئيات',
                'description:en' => 'video',
                'description:ar' => 'مرئيات',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::Video,
            ],
            [
                'id' => 3,
                'title:en' => 'Audio',
                'title:ar' => 'صوتيات',
                'description:en' => 'audio',
                'description:ar' => 'صوتيات',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::Audio,
            ],
            [
                'id' => 4,
                'title:en' => 'Multi choice',
                'title:ar' => 'اختيار من متعدد',
                'description:en' => 'MULTI_CHOICE',
                'description:ar' => 'اختيار من متعدد',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::MULTI_CHOICE,
            ],
            [
                'id' => 5,
                'title:en' => 'Drag & Drop',
                'title:ar' => 'سحب والقاء',
                'description:en' => 'DRAG_DROP',
                'description:ar' => 'سحب والقاء',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::DRAG_DROP,
            ],
            [
                'id' => 6,
                'title:en' => 'PDF',
                'title:ar' => 'كتب الكترونية',
                'description:en' => 'PDF',
                'description:ar' => 'كتب الكترونية',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::PDF,
            ],
            [
                'id' => 7,
                'title:en' => 'Flash',
                'title:ar' => 'ملقات فلاش',
                'description:en' => 'FLASH',
                'description:ar' => 'ملقات فلاش',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::FLASH,
            ],
            [
                'id' => 8,
                'title:en' => 'Picture',
                'title:ar' => 'صور',
                'description:en' => 'PICTURE',
                'description:ar' => 'صور',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::PICTURE,
            ],
            [
                'id' => 9,
                'title:en' => 'Matching',
                'title:ar' => 'توصيل',
                'description:en' => 'MATCHING',
                'description:ar' => 'توصيل',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::MATCHING,
            ],
            [
                'id' => 10,
                'title:en' => 'Multi matching',
                'title:ar' => 'توصيل متعدد',
                'description:en' => 'MULTIPLE_MATCHING',
                'description:ar' => 'توصيل متعدد',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::MULTIPLE_MATCHING,
            ],
            [
                'id' => 11,
                'title:en' => 'Page',
                'title:ar' => 'صفحات الكترونية',
                'description:en' => 'PAGE',
                'description:ar' => 'صفحات الكترونية',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::PAGE,
            ],
            [
                'id' => 12,
                'title:en' => 'Complete',
                'title:ar' => 'اكمل',
                'description:en' => 'complete',
                'description:ar' => 'اكمل',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::COMPLETE,
            ],
            [
                'id' => 13,
                'title:en' => 'Hotspot',
                'title:ar' => 'النقاط الساخنة',
                'description:en' => 'hotspot',
                'description:ar' => 'النقاط الساخنة',
                'is_active' => 1,
                'slug' => LearningResourcesEnums::HOTSPOT,
            ],
        ];


        foreach ($data as $resource) {
            Resource::create($resource);
        }
    }
}
