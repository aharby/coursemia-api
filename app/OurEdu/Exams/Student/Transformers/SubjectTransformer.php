<?php

namespace App\OurEdu\Exams\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{
    private $params;
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'actions'
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {

//        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'educational_term' => (string)($subject->educationalTerm->title ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
            'start_date' => (string)$subject->start_date,
            'end_date' => (string)$subject->end_date,
            'is_active' => (boolean)$subject->is_active,
            'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            'subject_library_text' => $subject->subject_library_text,


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



    public function includeActions($subject)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.subjects.view-subject', ['subjectId' => $subject->id]),
            'label' => trans('subjects.View subject'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_SUBJECT
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
