<?php


namespace App\OurEdu\AcademicYears\Transformers;


use League\Fractal\TransformerAbstract;

class DifficultyLevelsLookUpTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [
    ];

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * @param $difficultyLevel
     * @return array
     */
    public function transform($difficultyLevel)
    {
        return [
            'id' => $difficultyLevel->id,
            'name' => $difficultyLevel->title,
            'slug' => $difficultyLevel->slug,
        ];
    }
}

