<?php

namespace App\OurEdu\Dashboard\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;

class DashBoardController extends BaseController
{
    public $module;
    private $parent;

    public function __construct()
    {
        $this->module = 'dashboard';
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $data['page_title'] = trans('app.Dashboard');
        return view($this->parent.'.'.$this->module . '.index', $data);
    }
}