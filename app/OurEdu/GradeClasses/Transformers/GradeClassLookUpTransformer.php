<?php


namespace App\OurEdu\GradeClasses\Transformers;


use League\Fractal\TransformerAbstract;

class GradeClassLookUpTransformer extends TransformerAbstract
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
     * @param $class
     * @return array
     */
    public function transform($class)
    {
        return [
            'id' => $class->id,
            'name' => $class->title
        ];
    }
}

