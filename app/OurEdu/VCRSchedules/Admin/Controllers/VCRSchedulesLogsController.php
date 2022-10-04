<?php

namespace App\OurEdu\VCRSchedules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;


class VCRSchedulesLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'vcrSchedules';
        $this->title = trans('vcr_schedule.VCRSchedules');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listVCRSchedulesLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[VCRSchedule::class,'VCRSchedule'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List VCRSchedules Logs');
        $data['breadcrumb'] = [trans('navigation.VCRSchedules') => route('admin.vcr_schedules.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
