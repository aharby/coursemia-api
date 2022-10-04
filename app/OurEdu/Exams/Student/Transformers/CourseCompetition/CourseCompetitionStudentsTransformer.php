<?php

namespace App\OurEdu\Exams\Student\Transformers\CourseCompetition;

use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class CourseCompetitionStudentsTransformer extends TransformerAbstract
{
    public function __construct(private Exam $exam)
    {
    }

    public function transform(Student $student)
    {
        $studentScore =  $student->pivot->result;
        $examQuestionsCount =  $this->exam->questions()->count();

        $totalCorrectAnswers = CompetitionQuestionStudent::where('student_id' , $student->id)
            ->where('exam_id' , $this->exam->id)
            ->sum('is_correct_answer');
        return [
            'id' => (int) $student->user->id,
            'name' => (string)$student->user->name,
            "result" =>  (string) ($examQuestionsCount > 0 ? $studentScore .'/'.$examQuestionsCount:0),
            'student_rank' => (string) ($student->pivot->is_finished) ? getOrdinal($student->pivot->rank):trans("exam.calculating rank in progress"),
            'profile_picture' => (string) imageProfileApi($student->user->profile_picture),
            'total_correct_answers' => (int) $totalCorrectAnswers,
        ];

    }

}
