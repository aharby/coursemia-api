<?php


namespace App\OurEdu\LearningPerformance\Parent\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentFeedbackTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'studentUser',
    ];


    public function transform(LearningPerformance $learningPerformance)
    {
        $numberTakenExams = $learningPerformance->student->exams()
            ->where('subject_id', $learningPerformance->subject->id)
            ->where('type', ExamTypes::EXAM)->count();
        return [
            'id' => Str::uuid(),
            'student_order' =>  $learningPerformance->studentOrderInGeneralExams . '/' . $learningPerformance->studentInGeneralExamsCount,
            'number_of_taken_exams' => (int)$numberTakenExams,
            'success_rate' =>  $learningPerformance->success_rate . '%',
            'time' => getStudentSubjectTimeInHours($learningPerformance->subject ,  $learningPerformance->student)  . " " . trans('subject.Hours'),
            'solving_speed_percentage_order' =>  $learningPerformance->solving_speed_percentage_order . '/' . $learningPerformance->countStudentsBySolvingSpeed, // according to all students
            'subject_progress_percentage_order' =>  $learningPerformance->subject_progress_percentage_order . '/' . $learningPerformance->studentsProgressCount, // according to all students
            'exams_count_order' =>  $learningPerformance->exams_count_order . '/'. $learningPerformance->countExamStudents, // according to all students
        ];
    }


    public function includeStudentUser(LearningPerformance $learningPerformance)
    {
        if(isset($learningPerformance->student->user_id)){
            $user= User::find($learningPerformance->student->user_id);
            return $this->item($user, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }
}

