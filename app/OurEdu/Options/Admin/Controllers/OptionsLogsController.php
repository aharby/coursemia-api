<?php

namespace App\OurEdu\Options\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Options\Option;


class OptionsLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'options';
        $this->title = trans('options.Options');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listOptionsLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[Option::class,'option'])
            ->where('auditable_id',$id)
            ->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Options Logs');
        $data['breadcrumb'] = [trans('navigation.Options') => route('admin.options.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
