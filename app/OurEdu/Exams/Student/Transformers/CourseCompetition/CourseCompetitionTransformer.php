<?php

namespace App\OurEdu\Exams\Student\Transformers\CourseCompetition;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CourseCompetitionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
        'competitionStudents',
        'competitionUser',
        'CompetitionOrderedStudents',
    ];

    public function __construct(public $params = [])
    {
    }

    public function transform(Exam $exam)
    {
        $diff = Carbon::now()->lessThanOrEqualTo(Carbon::parse($exam->start_time));
        $time = 0;

        if($diff){
           $time = Carbon::now()->diffInSeconds(Carbon::parse($exam->start_time));
        }

        $transformerData = [
            'id' => (int)$exam->id,
            'title' => (string)trans(
                'app.competition_on:',
                [
                'title' => $exam->title
                ]
            ),
            'questions_number' => $exam->questions_number,
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,
            'difficulty_level' => $exam->difficulty_level,
            'start_time' => $exam->start_time,
            'end_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            "time_to_solve" => $exam->time_to_solve,
            "course_id" => (int)$exam->course_id,
            'time_left' => $time
        ];

        return $transformerData;
    }

    public function includeCompetitionStudents(Exam $exam)
    {
        $students = $this->getStudentsAndOrder($exam);

        return $this->collection(
            $students,
            new CompetitionStudentTransformer($exam),
            ResourceTypesEnums::COMPETITION_STUDENT
        );
    }


    public function includeCompetitionUser(Exam $exam)
    {
        $student = $this->getStudentsAndOrder($exam)->where('id',auth()->user()->student->id)->first();

        if ($student) {
            return $this->item(
                $student,
                new CompetitionStudentTransformer($exam),
                ResourceTypesEnums::COMPETITION_STUDENT
            );
        }
    }

    public function includeCompetitionOrderedStudents(Exam $exam)
    {
        $students = $this->getStudentsAndOrder($exam);

        return $this->collection(
            $students,
            new CompetitionStudentTransformer($exam),
            ResourceTypesEnums::COMPETITION_STUDENT

         );
    }

    private function getStudentsAndOrder(Exam $exam){

        $students = $exam->competitionStudents()->orderByPivot('result', 'DESC')->get();
        $rank = 1;
        $previous = null;

        foreach ($students as $s) {
            if ($previous && $previous->pivot->result != $s->pivot->result) {
                $rank++;
            }
            $previous = $s;

            $s->order = $rank;
        }

        return $students;
    }

}
