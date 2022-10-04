<?php


namespace App\OurEdu\Schools\Transformers;


use League\Fractal\TransformerAbstract;

class SchoolLookUpTransformer extends TransformerAbstract
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
     * @param $school
     * @return array
     */
    public function transform($school)
    {
        return [
            'id' => $school->id,
            'name' => $school->name
        ];
    }
}
