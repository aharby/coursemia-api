<?php


namespace App\OurEdu\Subjects\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\AjaxController;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;

class AjaxSubjectController extends AjaxController
{
    private $repository;

    public function __construct(SubjectRepositoryInterface $interface)
    {
        parent::__construct();
        $this->repository = $interface;
    }

    public function getSubjects()
    {
        $subjects = $this->repository->pluckSubjectsFilteredToArray(
            request('country_id'),
            request('educational_system_id'),
            request('grade_class_id'),
            request('academical_years_id')
        );
        return response()->json(
            [
                'status' => '200',
                'subjects' => $subjects
            ]
        );
    }
}
