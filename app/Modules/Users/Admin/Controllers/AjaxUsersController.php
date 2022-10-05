<?php


namespace App\Modules\Users\Admin\Controllers;

use App\Modules\Users\UserEnums;
use App\Modules\BaseApp\Controllers\AjaxController;
use App\Modules\Users\Repository\UserRepositoryInterface;
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
