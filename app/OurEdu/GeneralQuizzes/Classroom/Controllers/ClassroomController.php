<?php


namespace App\OurEdu\GeneralQuizzes\Classroom\Controllers;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Classroom\Transformers\StudentTransformer;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;

class ClassroomController extends BaseApiController
{
    public function getClassroomStudents(Classroom $classroom)
    {
        $classrooms = [];
        if (auth()->user() && auth()->user()->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $classroomsIDs = auth()->user()->schoolInstructorSessions()->distinct()->pluck('classroom_id')->toArray();

            if (count($classroomsIDs)) {
                $classrooms = Classroom::query()->whereIn("id", $classroomsIDs);

                if (isset($this->params['grade_class_id'])) {
                    $classrooms->whereHas(
                        "branchEducationalSystemGradeClass",
                        function (Builder $branchEducationalSystemGradeClass) {
                            $branchEducationalSystemGradeClass->where("grade_class_id", "=", $this->params['grade_class_id']);
                        }
                    );
                }
                $classrooms = $classrooms->get();
            }
        }

        if(!$classrooms->contains('id' , $classroom['id']))
        {
            $return['status'] = 422;
            $return['detail'] = trans('api.The school instructor not assigned on this classroom');
            $return['title'] = trans('api.The school instructor not assigned on this classroom');
            return formatErrorValidation($return);
        }



        return $this->transformDataModInclude($classroom->students,'', new StudentTransformer(), ResourceTypesEnums::STUDENT);
    }
}
