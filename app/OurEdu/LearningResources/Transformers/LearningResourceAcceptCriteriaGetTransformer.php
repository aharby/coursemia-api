<?php


namespace App\OurEdu\LearningResources\Transformers;


use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\LearningResources\Resource;

use App\OurEdu\Options\Repository\OptionRepository;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class LearningResourceAcceptCriteriaGetTransformer extends TransformerAbstract
{
    protected $learningResource;

    protected array $defaultIncludes = [
    ];
    private $minimalData;

    public function __construct($learningResource = '', $minimalData = false)
    {
        $this->learningResource = $learningResource;
        $this->minimalData = $minimalData;
    }

    /**
     * @param Resource $resource
     * @return array
     */
    public function transform($acceptCriteria)
    {
        $data = [
            'id' => Str::uuid(),
        ];

        if($this->minimalData && isset($acceptCriteria['description']))
            unset($acceptCriteria['description']);

        foreach ($acceptCriteria as $key => $value) {
            if (in_array($key, LearningResourcesEnums::KEY_OPTIONS)) {

                if (isset($value) && (int)($value)) {

                    $value = (new OptionRepository())->find($value)->slug ?? '';
                }
            }
            $data[$key] = $value;
        }

        return $data;
    }


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


}

