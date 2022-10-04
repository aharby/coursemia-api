<?php

namespace App\OurEdu\SubjectPackages\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Country;
use App\OurEdu\SubjectPackages\Package;


class SubjectPackagesLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'subjectPackages';
        $this->title = trans('app.SubjectPackages');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listSubjectPackagesLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[Package::class,'package'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Subject Packages Logs');
        $data['breadcrumb'] = [trans('navigation.Subject Packages') => route('admin.subjectPackages.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
