<?php

namespace App\OurEdu\Exams\Instructor\Transformers;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;

class CourseCompetitionStudentsTransformer extends TransformerAbstract
{
    public function __construct(private Exam $exam)
    {
    }

    public function transform(Student $student)
    {
        $studentScore =  $student->pivot->result;
        $examQuestionsCount = $this->exam->questions()->count();
        return [
            'id' => $student->user->id,
            'name' => $student->user->name,
            'profile_picture' => (string) imageProfileApi($student->user->profile_picture),
            'result' => is_null($studentScore)  ?  "-" :  $studentScore  . ' / ' . $examQuestionsCount,
            'student_rank' => (string) ($student->pivot->is_finished) ? getOrdinal($student->pivot->rank):trans("exam.calculating rank in progress")
        ];

    }

}
