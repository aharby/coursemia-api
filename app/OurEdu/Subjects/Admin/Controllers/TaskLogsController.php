<?php

namespace App\OurEdu\Subjects\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Subjects\Models\SubModels\Task;


class TaskLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'tasks';
        $this->title = trans('app.Tasks');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listTaskLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[Task::class,'task'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Task Logs');
        $data['breadcrumb'] = [trans('navigation.Tasks') => route('admin.subjects.get.index.tasks')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
