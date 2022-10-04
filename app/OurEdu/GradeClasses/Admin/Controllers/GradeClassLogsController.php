<?php

namespace App\OurEdu\GradeClasses\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;


class GradeClassLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'grade_classes';
        $this->title = trans('grade_classes.Grade Class');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listgradeClassesLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[GradeClass::class,'gradeClasses'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Grade Class Logs');
        $data['breadcrumb'] = [trans('navigation.Grade Class') => route('admin.gradeClasses.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
