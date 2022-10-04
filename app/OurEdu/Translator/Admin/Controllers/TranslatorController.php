<?php

namespace App\OurEdu\Translator\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;

class TranslatorController extends Controller {

    public $module;
    private $parent;


    public function __construct() {
        $this->module = 'translator';
        $this->parent = ParentEnum::ADMIN;

        $this->title = trans('app.Translator');
    }

    public function getIndex() {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Translator');
        $data['rows'] = getListOfFiles(resource_path() . '/lang/en');
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getEdit($file) {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Translator');

        if (!is_array(trans($file))) {
            return abort(404);
        }
        foreach (config("translatable.locales") as $lang) {
            $data['rows'][$lang] = trans($file, [], $lang);
        }
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function postEdit($file) {
        foreach (config("translatable.locales") as $lang) {
            $text = "<?php \n return [\n";
            foreach (request($lang) as $key => $value) {
                $text .= "'{$key}' => '{$value}',\n";
            }
            $text .= "];";
            file_put_contents(resource_path() . '/lang/' . $lang . '/' . $file . '.php', $text);
        }
        flash(trans('app.Update successfully'))->success();
        return back();
    }

}
