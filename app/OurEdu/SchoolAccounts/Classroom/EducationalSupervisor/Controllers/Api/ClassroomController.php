<?php


namespace App\OurEdu\SchoolAccounts\Classroom\EducationalSupervisor\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\SchoolAccounts\Classroom\EducationalSupervisor\Transformars\ClassroomTransform;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;

class ClassroomController extends BaseApiController
{
    /**
     * @var ClassroomRepositoryInterface
     */
    private $classroomRepository;

    /**
     * ClassroomController constructor.
     * @param ClassroomRepositoryInterface $classroomRepository
     */
    public function __construct(ClassroomRepositoryInterface $classroomRepository)
    {
        $this->classroomRepository = $classroomRepository;
    }

    public function index()
    {
        $classrooms = $this->classroomRepository->getBranchClassroomClasses(auth()->user()->branch_id);

        return $this->transformDataMod($classrooms, new ClassroomTransform(), "classrooms");
    }
}
