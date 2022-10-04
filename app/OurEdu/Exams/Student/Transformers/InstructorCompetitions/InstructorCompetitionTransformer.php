<?php


namespace App\OurEdu\Exams\Student\Transformers\InstructorCompetitions;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use League\Fractal\TransformerAbstract;

class InstructorCompetitionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
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
        $transformerData = [
            'id' => (int)$exam->id,
            'title' => (string)examTitle($exam->type, $exam->title),
            'questions_number' => $exam->questions_number,
            'instructor' => $exam->creator->name,
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,
            'start_time' => $exam->start_time,
            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
        ];
        return $transformerData;
    }
}
