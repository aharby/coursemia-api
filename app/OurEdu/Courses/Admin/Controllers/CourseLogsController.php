<?php

namespace App\OurEdu\Courses\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Courses\Models\Course;


class CourseLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'courses';
        $this->title = trans('app.Courses');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listCoursesLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[Course::class,'course'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Courses Logs');
        $data['breadcrumb'] = [trans('navigation.Course') => route('admin.courses.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
