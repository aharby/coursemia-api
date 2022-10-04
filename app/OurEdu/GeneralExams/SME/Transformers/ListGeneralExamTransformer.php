<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\GeneralExamQuestionTransformer;

class ListGeneralExamTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralExam $exam)
    {
        $transformerDatat = [
            'id' => (int)$exam->id,
            'name' => $exam->name,
            'difficulty_level' => isset($exam->difficultyLevel->title) ? __('options.' . $exam->difficultyLevel->title) : '',
            'subject_format_subjects' => json_decode($exam->subject_format_subjects),
            'subject_id' => $exam->subject_id,
            'subject_name' => $exam->subject->name ?? '',
            'class' => $exam->subject->gradeClass->title ?? '',
            'date' => $exam->date ,
            'date_time' => $exam->date .' '.$exam->start_time.'-'. $exam->end_time,
            'start_time' => $exam->start_time,
            'end_time' => $exam->end_time,
            'is_published' => (bool)$exam->published_at,
            'published_at' => (string)$exam->published_at,
            'created_at' => (string)$exam->created_at,
        ];

        return $transformerDatat;
    }


    public function includeActions(GeneralExam $exam)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.general_exams.view', ['exam' => $exam]),
            'label' => trans('app.View'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_GENERAL_EXAM
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
