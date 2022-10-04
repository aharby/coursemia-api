<?php


namespace App\OurEdu\LearningResources\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\LearningResources\Resource;

use League\Fractal\TransformerAbstract;

class LearningResourceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'learningResourceAcceptCriteria'
    ];


    /**
     * @param Resource $resource
     * @return array
     */
    public function transform(Resource $resource)
    {

        return [
            'id' => (int)$resource->id,
            'title' => (string)$resource->title,
            'description' => (string)$resource->description,
            'slug' => (string)$resource->slug,
            'is_active' => (bool)$resource->is_active,
//            'accept_criteria' => json_encode($acceptCriteria),


        ];
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
    public function includeLearningResourceAcceptCriteria($resource)
    {
        $acceptCriteria = LearningResourcesEnums::LearningResources[$resource->slug] ?? [];

//        $acceptCriteria = array_map(function ($accept) {
//            return '' ;
//        }, $acceptCriteria);

        return $this->item($acceptCriteria, new LearningResourceAcceptCriteriaTransformer($resource->slug), ResourceTypesEnums::LEARNING_RESOURCE_ACCEPT_CRITERIA_FIELD);

    }

}

