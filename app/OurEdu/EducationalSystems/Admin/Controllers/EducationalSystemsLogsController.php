<?php

namespace App\OurEdu\EducationalSystems\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;


class EducationalSystemsLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'educationalSystems';
        $this->title = trans('app.Educational Systems');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listEducationalSystemsLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[EducationalSystem::class,'educationalSystems'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Educational Systems Logs');
        $data['breadcrumb'] = [trans('navigation.Educational Systems') => route('admin.educationalSystems.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
