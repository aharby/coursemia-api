<?php

namespace App\OurEdu\AppVersions\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\AppVersions\Repository\AppVersionRepositoryInterface;
use App\OurEdu\AppVersions\Requests\AppVersionRequest;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Users\Admin\Middleware\IsSuperAdmin;

class AppVersionController extends Controller
{

    public $module;
    public $parent;
    public $title;
    /**
     * @var AppVersionRepositoryInterface
     */
    private $appVersionRepo;

    public function __construct(AppVersionRepositoryInterface $appVersionRepInt)
    {
        $this->middleware(IsSuperAdmin::class);
        $this->module = 'app_versions';
        $this->title = trans('app.App Versions');
        $this->appVersionRepo = $appVersionRepInt;
        $this->parent = ParentEnum::ADMIN;
    }

    public function getEdit()
    {
        $data['page_title'] = trans('app.Edit') . " " . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.app.versions.get.edit')];
        $data['rows'] = $this->appVersionRepo->get();
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function postEdit(AppVersionRequest $request)
    {
        $rows = $this->appVersionRepo->get();
        if ($rows) {
            foreach ($rows as $row) {
                $row->update(['version' => $request->get($row->id)]);
            }
        }
        flash(trans('app.Update successfully'))->success();
        return back();
    }
}
