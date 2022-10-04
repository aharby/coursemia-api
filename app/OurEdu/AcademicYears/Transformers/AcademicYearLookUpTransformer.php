<?php


namespace App\OurEdu\AcademicYears\Transformers;


use League\Fractal\TransformerAbstract;

class AcademicYearLookUpTransformer extends TransformerAbstract
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
     * @param $academicYear
     * @return array
     */
    public function transform($academicYear)
    {
        return [
            'id' => $academicYear->id,
            'name' => $academicYear->title
        ];
    }
}

