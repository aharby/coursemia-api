<?php
namespace App\OurEdu\SchoolAccounts\SPA\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\SchoolRequests\Repository\SchoolRequestRepository;

class SPAController extends BaseController
{
    private $repository;
    private $module;
    private $parent;

    public function __construct(SchoolRequestRepository $repository)
    {
        $this->module = 'school_requests';
        $this->repository = $repository;
        $this->parent = ParentEnum::ADMIN;

    }

    public function getVueSupervisor()
    {

        return view('school_supervisor.vue_blade.school_supervisor_layout');
    }


}
