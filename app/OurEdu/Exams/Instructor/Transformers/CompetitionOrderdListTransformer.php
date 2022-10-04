<?php

namespace App\OurEdu\Exams\Instructor\Transformers;

use App\OurEdu\Exams\Models\Exam;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Str;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Student\Transformers\Competitions\CompetitionStudentTransformer;
class CompetitionOrderdListTransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
        'students',
        'competitionStudents'
    ];

    public function __construct(public Exam $exam, $params = [])
    {
        $this->params = $params;
    }

    public function transform($data)
    {
        $questionCount = $this->exam->questions()->count();
        return [
            'id' =>Str::uuid(),
            'result' => is_null($data->result)  ?  "-" :  $data->result  . ' / ' . $questionCount,
            'rank' => (string) ($data->is_finished) ? getOrdinal($data->rank):trans("exam.calculating rank in progress")

        ];

    }

    public function includeStudents($data)
    {
        return $this->collection($data, new CourseCompetitionStudentsTransformer($data->exam),ResourceTypesEnums::COMPETITION_STUDENT);
    }

    public function includeCompetitionStudents($data)
    {
        return $this->collection($data, new CompetitionStudentTransformer($data->exam, $this->params),ResourceTypesEnums::COMPETITION_STUDENT);
    }

}
