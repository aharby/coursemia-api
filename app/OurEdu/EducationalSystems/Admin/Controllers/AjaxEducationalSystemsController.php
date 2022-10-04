<?php


namespace App\OurEdu\EducationalSystems\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\AjaxController;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;

class AjaxEducationalSystemsController extends AjaxController
{
    private $repository;

    public function __construct(EducationalSystemRepositoryInterface $interface)
    {
        parent::__construct();
        $this->repository = $interface;
    }
    public function getEducationalSystem()
    {
        if ($countryId = request('country_id')) {
            $educationalSystem = $this->repository->pluckByCountryId($countryId);

            return response()->json(
                [
                    'status' => '200',
                    'educationSystem' => $educationalSystem
                ]
            );
        }
    }
}
