<?php


namespace App\OurEdu\EducationalSystems\Transformers;


use League\Fractal\TransformerAbstract;

class EducationalSystemLookUpTransformer extends TransformerAbstract
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
     * @param $educationalSystem
     * @return array
     */
    public function transform($educationalSystem)
    {
        return [
            'id' => $educationalSystem->id,
            'name' => $educationalSystem->name
        ];
    }
}

