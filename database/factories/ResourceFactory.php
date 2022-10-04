<?php

namespace Database\Factories;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\LearningResources\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Resource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $resources =
            [
                [
                    'title' => LearningResourcesEnums::MULTI_CHOICE,
                    'slug' => LearningResourcesEnums::MULTI_CHOICE,
                ],
                [
                    'title' => LearningResourcesEnums::TRUE_FALSE,
                    'slug' => LearningResourcesEnums::TRUE_FALSE,
                ],
                [
                    'title' => LearningResourcesEnums::Audio,
                    'slug' => LearningResourcesEnums::Audio,
                ],
                [
                    'title' => LearningResourcesEnums::FLASH,
                    'slug' => LearningResourcesEnums::FLASH,
                ],
                [
                    'title' => LearningResourcesEnums::DRAG_DROP,
                    'slug' => LearningResourcesEnums::DRAG_DROP,
                ],
                [
                    'title' => LearningResourcesEnums::PDF,
                    'slug' => LearningResourcesEnums::PDF,
                ],
            ];

        $resource = $this->faker->randomElement($resources);

        return [
            'title' => $resource['title'],
            'description' => $resource['title'],
            'is_active' => 1,
            'slug' => $resource['slug'],

        ];
    }
}
