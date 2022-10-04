<?php

namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\CreatePeriodicTestUseCase;

use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Carbon\Carbon;

class CreatePeriodicTestUsecase implements CreatePeriodicTestUseCaseInterface
{

    private $generalQuizRepo;
    private $classroomRepo;
    private $studentRepo;
    private $user;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        ClassroomRepositoryInterface $classroomRepo,
        StudentRepositoryInterface $studentRepo
    ){
        $this->generalQuizRepo = $generalQuizRepo;
        $this->classroomRepo = $classroomRepo;
        $this->studentRepo = $studentRepo;
        $this->user = Auth::guard('api')->user();
    }

    public function createPeriodicTest($data): array
    {
        $gradeClassClassroomsIds = $this->classroomRepo->getClassroomsByBranchAndGradeclass(
            $this->user->branch_id,
            $data->grade_class_id
        );
        $validationErrors = $this->createPeriodicTestValidations($data,$gradeClassClassroomsIds);

        if ($validationErrors) {
            return $validationErrors;
        }
        $additionalData['quiz_type'] = GeneralQuizTypeEnum::PERIODIC_TEST;
        $additionalData['branch_id'] = $this->user->branch_id;
        $additionalData['grade_class_id'] = $data['grade_class_id'];
        $data['test_time'] = $data['test_time']*60;

        $subject = Subject::findOrFail($data['subject_id']);


        $additionalData['school_account_id'] = $this->user->schoolInstructorBranch->school_account_id;
        $periodicTest = $this->generalQuizRepo->create(array_merge($data->toArray(), $additionalData));

        $classroomIds = isset($data->classrooms)?$data->classrooms->pluck('id')->toArray():[];
        $studentIds = isset($data->students)?$data->students->pluck('id')->toArray():[];

        $classroomsCount = count($classroomIds);

        // case one if there's no given classrooms
        if($classroomsCount == 0){

            //if there's a given students
            if(count($studentIds)>0){
                $this->generalQuizRepo->saveGeneralQuizStudents($periodicTest,$studentIds);
            }else{
                $this->generalQuizRepo->saveGeneralQuizClassrooms($periodicTest,$gradeClassClassroomsIds);
            }

        }elseif($classroomsCount>0){
            // case two if there's a given classrooms
            $this->generalQuizRepo->saveGeneralQuizClassrooms($periodicTest,$classroomIds);

            // if count of classroom is 1 and there's  a given students
            if($classroomsCount == 1 && count($studentIds)>0){
                $this->generalQuizRepo->saveGeneralQuizStudents($periodicTest,$studentIds);
            }
        }

        $this->generalQuizRepo->saveGeneralQuizSections($periodicTest, $data->subject_sections);

        $useCase['periodicTest'] = $periodicTest;
        $useCase['meta'] = [
            'message' => trans('general_quizzes.periodicTest_created')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    protected function createPeriodicTestValidations($data,$gradeClassClassroomsIds){

        $classroomIds = isset($data->classrooms)?$data->classrooms->pluck('id')->toArray():[];
        $studentIds = isset($data->students)?$data->students->pluck('id')->toArray():[];

        // validate that given classrooms belongs to this branch and grade class
        if(count($classroomIds) > 0){
            $invalidClassrooms = array_values(array_diff($classroomIds, $gradeClassClassroomsIds));
            if(count($invalidClassrooms) >0){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.invalid gradeclass classrooms');
                $useCase['title'] = 'Invalid Classrooms';
                return $useCase;
            }
        }

        // validate that the given  students belongs to the given classroom
        if(count($classroomIds) == 1 && count($studentIds) > 0){
            $classroomsStudents = $this->studentRepo->getClassroomStudentsByUserIds($studentIds,$classroomIds);
            $invalidStudents = array_values(array_diff($studentIds, $classroomsStudents));
            if(count($invalidStudents) >0){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.Invalid students');
                $useCase['title'] = 'Invalid students';
                return $useCase;
            }

        }

        // if there is no given classrooms and there's students then validate that these students belongs to this grade class
        if(count($classroomIds) == 0  && count($studentIds) > 0){
            $classroomsStudents = $this->studentRepo->getClassroomStudentsByUserIds($studentIds,$gradeClassClassroomsIds);
            $invalidStudents = array_values(array_diff($studentIds, $classroomsStudents));
            if(count($invalidStudents) >0){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.Invalid students');
                $useCase['title'] = 'Invalid students';
                return $useCase;
            }
        }


        // In case we want no duplication per periodic test

        // $periodicTest = GeneralQuiz::query()
        //     ->where("branch_id", "=", auth()->user()->branch_id)
        //     ->where('grade_class_id', "=", $data['grade_class_id'])
        //     ->where("quiz_type", "=", QuizTypesEnum::PERIODIC_TEST)
        //     ->where(function ($query) use ($data) {
        //         $query->whereBetween('start_at', [$data['start_at'], $data['end_at']])
        //             ->orWhereBetween('end_at', [$data['start_at'], $data['end_at']])
        //             ->orWhere(function ($nestedQuery) use ($data) {
        //                 $nestedQuery->where("start_at", "<=", $data['end_at'])
        //                     ->where("end_at", ">=", $data['start_at']);
        //             });
        //     })->where(function ($query) use($studentIds,$classroomIds){
        //         $query->whereHas('students', function ($q) use ($studentIds) {
        //             $q->whereIn('id', $studentIds);
        //         })
        //         ->orWhereHas('classrooms', function ($qu)use($classroomIds) {
        //             $qu->whereIn('id', $classroomIds);
        //         });
        //     })
        //     ->first();


        // if($periodicTest){
            // $useCase['status'] = 422;
            // $useCase['title'] = 'Conflict exist';
            // $useCase['detail'] = trans('general_quizzes.there is a conflict with an existed periodicTest time', [
            //     "from" => Carbon::parse($periodicTest->start_at)->format("d/m h:i a"),
            //     "to" => Carbon::parse($periodicTest->end_at)->format("d/m h:i a"),
            //     "instructor" => $periodicTest->creator->name ?? "",
            //     "grade_class" => $periodicTest->gradeClass->title ?? "",
            //     "subject" => $periodicTest->subject->name ?? "",
            // ]);
            // return $useCase;
        // }

    }
}
