<?php


namespace App\OurEdu\Users\Admin\Controllers;

use App\OurEdu\Users\UserEnums;
use App\OurEdu\BaseApp\Controllers\AjaxController;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;

class AjaxUsersController extends AjaxController
{
    private $repository;

    public function __construct(UserRepositoryInterface $interface)
    {
        parent::__construct();
        $this->repository = $interface;
    }

    public function getInstructors()
    {
        if ($subject_id = request('subject_id')) {
            $instructors = $this->repository->getPluckInstructorsBySubjectId($subject_id);
            return response()->json(
                [
                    'status' => '200',
                    'instructors' => $instructors
                ]
            );
        }
    }

    public function searchStudents()
    {
        $users = $this->repository->searchStudentsByEmail(request('q'));

        return response()->json(
            [
                'status' => '200',
                'users' => $users,
            ]
        );
    }

}
