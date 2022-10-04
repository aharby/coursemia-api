<?php

namespace App\OurEdu\Courses\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Courses\Models\SubModels\LiveSession;


class LiveSessionLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'liveSessions';
        $this->title = trans('app.Live Sessions');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listLiveSessionLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[LiveSession::class,'liveSession'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Live Sessions Logs');
        $data['breadcrumb'] = [trans('navigation.Live Sessions') => route('admin.liveSessions.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
