<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class CloneQuizzess extends Seeder
{
    public function __construct(ClassroomRepositoryInterface $classroomRepository)
    {
        $this->classroomRepository = $classroomRepository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalQuizzes = GeneralQuiz::query()->where('quiz_type','formative_test')->where('start_at','2022-02-01 09:00:00')->get();

        foreach ($generalQuizzes as $generalQuizz){
            $endAt = (new Carbon('2022-02-01 22:00:00'))->format('Y-m-d H:i:s');
            $retakenQuiz = $generalQuizz->replicate();
            $retakenQuiz->start_at  = '2022-02-01 14:00:00';
            $retakenQuiz->end_at  = $endAt;
            $retakenQuiz->published_at = now();
            $retakenQuiz->save();

            // questions
            $quizQuestions = $generalQuizz->questions()->pluck("id")->toArray();

            $retakenQuiz->questions()->sync($quizQuestions);

            $schoolAdmin = $generalQuizz->creator;
            $userSchools = $schoolAdmin
                ->schoolAdminAssignedSchools()
                ->pluck("school_accounts.id")
                ->toArray();

            $branches = SchoolAccountBranch::query()
                ->whereIn('school_account_id', $userSchools)
                ->whereHas(
                    'educationalSystems',
                    function (Builder $educationalSystem) use ($generalQuizz) {
                        $educationalSystem->where('educational_systems.id', '=', $generalQuizz->educational_system_id);
                    }
                )
                ->pluck("id")
                ->toArray();


            $getActualClassroom = $this->classroomRepository->getClassroomsByBranchesAndGradeClasses(
                $branches,
                $generalQuizz->grade_class_id,
                null,
                $generalQuizz->educational_system_id
            );
            $students = [];
            foreach ($getActualClassroom as $classroom)
            {
                $AllStudentsOfClassroom = $classroom->students()->pluck('user_id')->toArray();
                $studentTakeGeneralQuiz = $generalQuizz->studentsAnswered()->whereHas('user.student',function ($query) use ($classroom){
                    $query->where('classroom_id',$classroom->id);
                })->pluck('student_id')->toArray();
                $didntAttend = array_diff($AllStudentsOfClassroom,$studentTakeGeneralQuiz);
                foreach ($didntAttend as $didnotAttend){
                    $students[] = $didnotAttend;
                }
            }
//            dd();
//            dd(implode(',',$students),$generalQuizz->id);
//            $oldClassroom = $generalQuizz->classrooms()->pluck('classrooms.id')->toArray();
//
//            $newClassroomsOfNewQuizzes = array_diff($getActualClassroom,$oldClassroom);
//            dump($newClassroomsOfNewQuizzes);
//            if (count($newClassroomsOfNewQuizzes) > 0){
//                dump('yes');
//            }
//            $retakenQuiz->classrooms()->sync($newClassroomsOfNewQuizzes);
            $retakenQuiz->students()->sync($students);

        }
    }
}
