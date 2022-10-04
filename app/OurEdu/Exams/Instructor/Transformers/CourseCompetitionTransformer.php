<?php


namespace App\OurEdu\Exams\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use League\Fractal\TransformerAbstract;

class CourseCompetitionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'actions',
    ];

    public function transform(Exam $exam)
    {
        $transformerDatat = [
            'id' => (int)$exam->id,
            'title' => (string) examTitle($exam->type, $exam->title),
            'questions_numbers' => $exam->questions_number,
            'number_of_pages' => $exam->questions_number,
            'difficulty_level' => trans( 'difficulty_levels.'.$exam->difficulty_level),
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,
            'course_id' => $exam->course_id,
            'course_name' => $exam->course->name,
            'start_time' => $exam->start_time,
            'end_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            'time_to_solve' => round($exam->time_to_solve),
        ];
        return $transformerDatat;
    }

    public function includeActions(Exam $exam)
    {
        $actions = [];

        if($exam->is_finished) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.courseCompetitions.feedback', ['exam' => $exam->id]),
                'label' => trans('exam.students'),
                'method' => 'GET',
                'key' => APIActionsEnums::FEDBACK
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }

    }
}
