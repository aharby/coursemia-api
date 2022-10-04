<?php


namespace App\OurEdu\Countries\Transformers;


use League\Fractal\TransformerAbstract;

class CountryLookUpTransformer extends TransformerAbstract
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
     * @param $country
     * @return array
     */
    public function transform($country)
    {
        return [
            'id' => $country->id,
            'name' => $country->name
        ];
    }
}

