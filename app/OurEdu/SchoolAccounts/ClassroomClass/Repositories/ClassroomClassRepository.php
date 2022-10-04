<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\Repositories;


use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassroomClassRepository implements ClassroomClassRepositoryInterface
{
    /**
     * @var ClassroomClass
     */
    private $classroomClass;

    public function __construct(ClassroomClass $classroomClass)
    {
        $this->classroomClass = $classroomClass;
    }

    public function paginateWhereClassroom(int $classroom): LengthAwarePaginator
    {
        return $this->classroomClass->where('classroom_id',$classroom)->paginate();
    }


    /**
     * @param int $id
     * @return ClassroomClass
     * @throw ModelNotFoundException
     */
    public function findOrFail(int $id): ClassroomClass
    {
        return $this->classroomClass->findOrFail($id);
    }

    public function getByClassroom(Classroom $classroom)
    {
        return  ClassroomClass::query()
            ->with("instructor", "subject")
            ->where("classroom_id", "=", $classroom->id)
            ->get();
    }

    public function getSessions(ClassroomClass $class)
    {
        return ClassroomClassSession::query()
            ->where("classroom_class_id", "=", $class->id)
            ->get();
    }

    /**
     * @param int $id
     * @return ClassroomClass
     */
    public function find(int $id): ClassroomClass
    {
        return ClassroomClass::query()->find($id);
    }
}
