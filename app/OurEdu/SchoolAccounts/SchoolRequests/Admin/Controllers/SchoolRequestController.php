<?php
namespace App\OurEdu\SchoolAccounts\SchoolRequests\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\SchoolRequests\Repository\SchoolRequestRepository;

class SchoolRequestController extends BaseController
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

    public function listSchoolRequests()
    {
        $data['rows'] = $this->repository->all();
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function approveSchoolRequests($id){
        $schoolRequest = $this->repository->findOrFail($id);
        $this->repository->update($schoolRequest, ['status'=>'Approved']);
        return redirect()->back();
    }
}
