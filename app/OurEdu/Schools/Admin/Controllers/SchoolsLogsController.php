<?php

namespace App\OurEdu\Schools\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Schools\School;


class SchoolsLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'schools';
        $this->title = trans('schools.Schools');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listSchoolsLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[School::class,'school'])
            ->where('auditable_id',$id)
            ->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Schools Logs');
        $data['breadcrumb'] = [trans('navigation.Schools') => route('admin.schools.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
