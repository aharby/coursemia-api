<?php

namespace App\OurEdu\Config\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Config\Repository\ConfigRepositoryInterface;
use App\OurEdu\Config\Requests\ConfigRequest;
use App\OurEdu\Users\Admin\Middleware\IsSuperAdmin;
use Intervention\Image\Facades\Image;

class ConfigsController extends Controller {

    public $module;
    public $configRepository;
    public $parent;
    public $title;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->middleware(IsSuperAdmin::class);
        $this->module = 'configs';
        $this->title = trans('app.Configs');
        $this->configRepository = $configRepository;
        $this->parent = ParentEnum::ADMIN;
    }

    public function getEdit()
    {
        $data['page_title'] = trans('app.Edit') . " " . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.configs.get.edit')];
        $data['rows'] = $this->configRepository->getConfigsData();
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function postEdit(ConfigRequest $request)
    {
        $rows = $this->configRepository->get();
        if ($rows) {
            foreach ($rows as $row) {
                if ($row->field_type == 'file') {
                    $field = 'input_' . $row->id;
                    if ($request->hasFile($field)) {
                        $uploadPath = 'uploads';
                        $image = $request->file($field);
                        $fileName = strtolower(str_random(10)) . time() . '.' . $image->getClientOriginalExtension();
                        $request->file($field)->move($uploadPath, $fileName);
                        $filePath = $uploadPath . '/' . $fileName;
                        if ($filePath) {
                            $imageSizes = ['small' => 'resize,200x200', 'large' => 'resize,400x300'];
                            foreach ($imageSizes as $key => $value) {
                                $value = explode(',', $value);
                                $type = $value[0];
                                $dimensions = explode('x', $value[1]);
                                if (!File::exists($uploadPath . '/' . $key)) {
                                    @mkdir($uploadPath . '/' . $key);
                                    @chmod($uploadPath . '/' . $key, 0777);
                                }
                                $thumbPath = $uploadPath . '/' . $key . '/' . $fileName;
                                $image = Image::make($filePath);
                                if ($type == 'crop') {
                                    $image->fit($dimensions[0], $dimensions[1]);
                                }
                                else {
                                    $image->resize($dimensions[0], $dimensions[1], function ($constraint) {
                                        $constraint->aspectRatio();
                                    });
                                }
                                $image->save($thumbPath);
                            }
                            @unlink($filePath);
                        }
                        $row->value = $fileName;
                        $row->save();
                    }
                }
                else {
                    $row->value = request('input_' . $row->id);
                    $row->save();
                }
            }
        }
        \Cache::forget('configs');
        updateConfigsCache();
        flash(trans('app.Update successfully'))->success();
        return back();
    }

}
