<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\Repositories;


use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClassroomClassRepositoryInterface
{
    /**
     * @param int $classroom
     * @return LengthAwarePaginator
     */
    public function paginateWhereClassroom(int $classroom): LengthAwarePaginator;


    /**
     * @param int $id
     * @return ClassroomClass
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id):ClassroomClass;

    /**
     * @param int $id
     * @return ClassroomClass
     */
    public function find(int $id) : ClassroomClass;

    public function getByClassroom(Classroom $classroom);

    public function getSessions(ClassroomClass $class);
}
