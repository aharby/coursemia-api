<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Transformers\LearningResourceAcceptCriteriaGetOptionIntegerTransformer;
use App\OurEdu\LearningResources\Transformers\LearningResourceAcceptCriteriaGetTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class ResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'learningResourceAcceptCriteria'

    ];
    protected array $availableIncludes = [
        'subjectFormatSubjects',
        'actions'
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
        // no need for data of accepts criteria in case of get single section structure
        if(isset($this->params['without_accept_criteria']) && $this->params['without_accept_criteria']){
            $this->defaultIncludes = [];
        }
    }

    /**
     * @param ResourceSubjectFormatSubject $resourceSubjectFormatSubject
     * @return array
     */
    public function transform(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        if(isset($this->params['minimal_data']) && $this->params['minimal_data']){
            return [
                'id' => (int)$resourceSubjectFormatSubject->id,
                'resource_id' => (string) ($resourceSubjectFormatSubject->resource ? $resourceSubjectFormatSubject->resource->id : null),
                'slug' => (string) $resourceSubjectFormatSubject->resource ?  $resourceSubjectFormatSubject->resource->slug : null,
            ];
        }
        return [
            'id' => (int)$resourceSubjectFormatSubject->id,
            'resource_id' => (string) ($resourceSubjectFormatSubject->resource ? $resourceSubjectFormatSubject->resource->id : null),
            'slug' => (string) $resourceSubjectFormatSubject->resource ?  $resourceSubjectFormatSubject->resource->slug : null,
            'is_active' => (bool)$resourceSubjectFormatSubject->is_active,
            'is_editable' => (bool)$resourceSubjectFormatSubject->is_editable,
            'list_order_key' => $resourceSubjectFormatSubject->list_order_key,

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


    public function includeSubjectFormatSubjects(Subject $subject)
    {
        $subjectFormatSubjects = $subject->subjectFormatSubject()->orderBy('order', 'asc')->get();

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeLearningResourceAcceptCriteria(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        $acceptCriteria = json_decode($resourceSubjectFormatSubject->accept_criteria, true);
        if (is_array($acceptCriteria)) {
            if (isset($this->params['method'])) {
                return $this->item($acceptCriteria, new LearningResourceAcceptCriteriaGetOptionIntegerTransformer('',$this->params['minimal_data']??false), ResourceTypesEnums::LEARNING_RESOURCE_ACCEPT_CRITERIA_FIELD);
            } else {
                return $this->item($acceptCriteria, new LearningResourceAcceptCriteriaGetTransformer('',$this->params['minimal_data']??false), ResourceTypesEnums::LEARNING_RESOURCE_ACCEPT_CRITERIA_FIELD);
            }
        }
    }
}
