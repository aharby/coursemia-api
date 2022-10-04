<?php

namespace App\OurEdu\VCRSchedules\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class ScheduleSubjectTransformer extends TransformerAbstract
{
    private $params;
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'actions'
    ];

    public function __construct($params = [])
    {
    }

    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {


        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'educational_term' => (string)($subject->educationalTerm->title ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),


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




}
