<?php

namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;

use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class GeneralQuizStudentRepository implements GeneralQuizStudentRepositoryInterface
{
    public function create($data)
    {
        return GeneralQuizStudent::create($data);
    }

    public function findOrFail($quizId)
    {
        return GeneralQuizStudent::findOrFail($quizId);
    }

    public function update($quizId , $data)
    {
        return GeneralQuizStudent::findOrFail($quizId)->update($data);
    }

    public function findStudentGeneralQuiz($quizId , $studentId){

       return GeneralQuizStudent::where('general_quiz_id' , $quizId)->where('student_id' , $studentId)->first();
    }

    public function getStudentCorrectAnswersCount($quizId , $studentId){
        return GeneralQuizStudentAnswer::where('student_id' , $studentId)->where('general_quiz_id', $quizId)->where('is_correct' , 1)->count();
    }

    public function getStudentTotalAnswersCount($quizId,$studentId){
        return GeneralQuizStudentAnswer::where('student_id' , $studentId)->where('general_quiz_id', $quizId)->count();
    }

    public function getStudentCorrectAnswersScore($quizId , $studentId){
        return GeneralQuizStudentAnswer::where('student_id' , $studentId)
            ->where('general_quiz_id', $quizId)
            ->where('is_correct' , 1)->get()->sum('score');
    }

    // public function getStudentsOrder($subjectId){
    //     return GeneralQuizStudent::where('subject_id', $subjectId)->select(DB::raw( 'student_id , AVG(result) as result_average' ))
    //         ->groupBy('student_id')
    //         ->get()->pluck('result_average', 'student_id')->toArray();
    // }

    public function getGeneralQuizStudents(User $student, array $data = [])
    {
        $generalQuiz = GeneralQuiz::query()->where('quiz_type','!=',GeneralQuizTypeEnum::FORMATIVE_TEST)
            ->with(
                [
                    "subject",
                    "studentsAnswered" => function (HasMany $generalQuizStudent) use ($student) {
                        $generalQuizStudent->where("student_id", "=", $student->id)
                            ->where("is_finished", "=", true)
                            ->where("show_result", "=", true);
                    },
                ]
            )
            ->where(
                function (Builder $generalQuizBuilder) use ($student) {
                    $generalQuizBuilder->whereHas(
                        "students",
                        function (Builder $user) use ($student) {
                            $user->where("id", "=", $student->id);
                        }
                    )
                        ->orWhereHas(
                            'classrooms',
                            function (Builder $classrooms) use ($student) {
                                $classrooms->where('id', $student->student->classroom_id);
                            }
                        )
                        ->whereDoesntHave("students");
                }
            )
            ->where(
                // this block of code handle case of running general quizzes
                // so, this code check if the quiz time passed or the student took it, to show his answer
                function (Builder $generalQuizBuilder) use ($student) {
                    $generalQuizBuilder->where("end_at", "<=", Carbon::now())
                        ->orWhereHas(
                            "studentsAnswered",
                            function (Builder $generalQuizStudent) use ($student) {
                                $generalQuizStudent->where("student_id", "=", $student->id)
                                    ->where("is_finished", "=", true)
                                    ->where("show_result", "=", true);
                            }
                        );
                }
            );

        if (isset($data['start_date'])) {
            $generalQuiz->where("start_at", ">=", $data['start_date']);
        }

        if (isset($data['end_date'])) {
            $generalQuiz->where("start_at", "<", Carbon::parse($data['end_date'])->addDay());
        }

        if (isset($data['quiz_type'])) {
            $generalQuiz->where("quiz_type", "=", $data['quiz_type']);
        }

        if (isset($data['subject_id'])) {
            $generalQuiz->where("subject_id", "=", $data['subject_id']);
        }

        return $generalQuiz->orderByDesc("start_at")->paginate();
    }
}
