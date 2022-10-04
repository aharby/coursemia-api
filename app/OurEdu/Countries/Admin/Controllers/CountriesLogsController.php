<?php

namespace App\OurEdu\Countries\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Country;


class CountriesLogsController extends Controller
{
    private $title;
    private $module;
    private $parent;

    public function __construct() {
        $this->module = 'countries';
        $this->title = trans('app.Countries');
        $this->parent = ParentEnum::ADMIN;
    }

    public function listCountriesLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[Country::class,'country'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Countries Logs');
        $data['breadcrumb'] = [trans('navigation.Countries') => route('admin.countries.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

}
