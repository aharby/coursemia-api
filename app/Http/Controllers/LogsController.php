<?php

namespace App\Http\Controllers;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\VCRSessions\Models\VcrSupport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogsController extends Controller {
    public $module;

    public function __construct() {
        $this->module = 'support';
        $this->parent = 'admin';
        $this->title = trans('support');
    }

    public function storeLog() {
        \Illuminate\Support\Facades\Log::channel('slack')->error(request()->all());
    }

    public function envServer() {
        return response()->json([
            'env_key' => env('SERVER','env var empty')
                                ]);
    }




    public function getSupportLog()
    {
        $data['rows'] = VcrSupport::with(['user','branch'])->latest()->paginate();
        $data['page_title'] = $this->title;
        return view($this->parent . '.' . $this->module . '.index', $data);
    }
}
