<?php


namespace App\OurEdu\LearningPerformance\Parent\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use League\Fractal\TransformerAbstract;

class ExamsPerformanceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];


    public function transform(Exam $exam)
    {
        return [
            'id' => (int) $exam->id,
            'exam_name' => (string) $exam->title,
            'difficulty_level' => (string) trans('difficulty_levels.'.$exam->difficulty_level),
            'time' => $exam->created_at,
            'estimated_time' => (int) $exam->time_to_solve,
            'student_time' =>(int)  $exam->student_time_to_solve,
            'result' => (int) $exam->result,
        ];
    }

    public function includeActions(Exam $exam)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.parent.learningPerformance.get.examPerformance',
                ['examId' => $exam->id]),
            'label' => trans('exam.exam Recommendation'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_EXAM_RECOMMENDATIONS
        ];
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}

