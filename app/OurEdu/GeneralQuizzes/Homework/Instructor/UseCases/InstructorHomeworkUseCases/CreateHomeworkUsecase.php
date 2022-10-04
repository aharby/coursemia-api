<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases;

use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
class CreateHomeworkUsecase implements CreateHomeworkUseCaseInterface
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

    public function createHomeWork($data): array
    {
        $validationErrors = $this->createHomeworkValidations($data);

        if ($validationErrors) {
            return $validationErrors;
        }
        $additionalData['quiz_type'] = GeneralQuizTypeEnum::HOMEWORK;
        $additionalData['branch_id'] = $this->user->branch_id;

        $subject=Subject::findOrFail($data['subject_id']);

        if($subject){
            $additionalData['grade_class_id'] = $subject->grade_class_id;
        }

        $additionalData['school_account_id'] = $this->user->schoolInstructorBranch->school_account_id;
        $homework = $this->generalQuizRepo->create(array_merge($data->toArray(), $additionalData));

        $this->generalQuizRepo->saveGeneralQuizClassrooms($homework,$data->classrooms->pluck('id')->toArray());
        $this->generalQuizRepo->saveGeneralQuizSections($homework, $data->subject_sections);

        if(isset($data->students) && count($homework->classrooms) == 1){
            $this->generalQuizRepo->saveGeneralQuizStudents($homework,$data->students->pluck('id')->toArray());
        }

        $useCase['homework'] = $homework;
        $useCase['meta'] = [
            'message' => trans('general_quizzes.homework_created')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    protected function createHomeworkValidations($data){

        if(!isset($data->classrooms) || $data->classrooms->count() == 0){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('generalQuiz.classroom is required');
            $useCase['title'] = 'classrooms is required';
            return $useCase;
        }

        $classroomIds = $data->classrooms->pluck('id')->toArray();


        $homework = GeneralQuiz::query()
            ->where("branch_id", "=", auth()->user()->branch_id)
            ->where('subject_id', "=", $data->subject_id)
            ->where("quiz_type", "=", GeneralQuizTypeEnum::HOMEWORK)
            ->where('is_active',1)
            ->whereHas('classrooms',function($query) use($data,$classroomIds){
                $query->whereIn('id',$classroomIds);
            })
            ->where(function ($query) use($data){
                    $start_at  = $data->start_at;
                    $end_at = $data->end_at;
                    $query->whereBetween('start_at', [$start_at, $end_at])
                        ->orWhereBetween('end_at', [$start_at, $end_at])
                        ->orWhere(function ($nestedQuery) use ($start_at,$end_at) {
                            $nestedQuery->where("start_at", "<=", $end_at)
                                ->where("end_at", ">=", $start_at);
                        });
                })->first();
                
                if($homework){
                    $useCase['status'] = 422;
                    $useCase['detail'] = trans('general_quizzes.there is an existed homework for same subject at same time', [
                        "from" => \Carbon\Carbon::parse($homework->start_at)->format("d/m/Y h:i a"),
                        "to" => \Carbon\Carbon::parse($homework->end_at)->format("d/m/Y h:i a")
                    ]);
                    $useCase['title'] = 'classrooms has homework at same time';
                    return $useCase;
                }



        $branchClassroomsIds = $this->classroomRepo->getBranchClassroomsByIds($classroomIds,auth()->user()->branch_id,$data->subject_id);

        $invalidClassrooms = array_values(array_diff($classroomIds, $branchClassroomsIds));

        if(count($invalidClassrooms) >0){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.invalid branch classrooms');
            $useCase['title'] = 'Invalid Classrooms';
            return $useCase;
        }

        if(count($classroomIds) == 1 && (isset($data->students) && $data->students->count() > 0)){

            $studentIds = $data->students->pluck('id')->toArray();
            $classroomsStudents = $this->studentRepo->getClassroomStudentsByUserIds($studentIds,$classroomIds);
            $invalidStudents = array_values(array_diff($studentIds, $classroomsStudents));
            if(count($invalidStudents) >0){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.Invalid students');
                $useCase['title'] = 'Invalid students';
                return $useCase;
            }

        }
    }
}
