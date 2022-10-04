<?php


namespace App\OurEdu\Exams\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use League\Fractal\TransformerAbstract;

class InstructorCompetitionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Exam $exam)
    {
        $transformerDatat = [
            'id' => (int)$exam->id,
            'title' => (string) examTitle($exam->type, $exam->title),
            'questions_numbers' => $exam->questions_number,
            'number_of_pages' => $exam->questions_number,
            'difficulty_level' => trans('difficulty_levels.'.$exam->difficulty_level),
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,
            'vcr_session_id' => $exam->vcr_session_id,
            'start_time' => $exam->start_time,
            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            'time_to_solve' => round($exam->time_to_solve),
            'share_link' => getDynamicLink(
                 DynamicLinksEnum::STUDENT_DYNAMIC_URL,
                 [
                     'link_name' => 'joinInstructorCompetition',
                     'firebase_url' => env('FIREBASE_URL_PREFIX'),
                     'portal_url' => env('STUDENT_PORTAL_URL'),
                     'query_param' =>'competition_id%3D'.$exam->id.'%26target_screen%3D'.DynamicLinkTypeEnum::JOIN_COMPETITION
                 ]
             )
        ];
        return $transformerDatat;
    }

    public function includeActions($exam)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.instructorCompetitions.startInstructorCompetition',
                ['competitionId' => $exam->id]),
            'label' => trans('exam.Start competition'),
            'method' => 'GET',
            'key' => APIActionsEnums::START_COMPETITION
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
