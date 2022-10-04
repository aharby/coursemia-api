<?php


namespace App\OurEdu\Quizzes\Observers;


use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Illuminate\Support\Str;
use App\OurEdu\SchoolAccounts\Classroom;

class QuizObserver
{

    public function created(Quiz $quiz)
    {
        $classroomStudents = [];
        if($quiz->quiz_type == 'periodic_test'){
            $classroomsIds = array_unique(
                Classroom::where('branch_id',$quiz->creator->branch_id)
                ->pluck('id')->toArray()
            );
            $classroomStudents = Student::whereIn('classroom_id',$classroomsIds)->where('class_id',$quiz->grade_class_id)->get('id');
        }else{
            $classroomStudents = Student::where('classroom_id', $quiz->classroom_id)->get('id');
        }
        $allQuizStudentsDataArr = [];
        foreach ($classroomStudents as $classroomStudent){
            $allQuizStudentsDataArr[] = [
                'student_id' => $classroomStudent->id,
                'quiz_type' => $quiz->quiz_type,
                'subject_id' => $quiz->subject_id ,
            ];
        }

        $quiz->allStudentQuiz()->createMany($allQuizStudentsDataArr);

        $quiz->branch_id = $quiz->creator->branch_id ?? null;
        $quiz->save();
    }

    public function deleted(Quiz $quiz)
    {
        $quiz->allStudentQuiz()->delete();
    }
}
