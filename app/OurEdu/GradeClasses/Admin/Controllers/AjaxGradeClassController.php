<?php


namespace App\OurEdu\GradeClasses\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\AjaxController;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;

class AjaxGradeClassController extends AjaxController
{
    private $repository;

    public function __construct(GradeClassRepositoryInterface $interface)
    {
        parent::__construct();
        $this->repository = $interface;
    }

    public function getGradeClasses()
    {
        if ($countryId = request('country_id')) {
            $gradeClasses = $this->repository->pluckByCountryId($countryId);

            return response()->json(
                [
                    'status' => '200',
                    'gradeClasses' => $gradeClasses
                ]
            );
        }
    }
}
