<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\App;

class ErrorResponseException extends Exception
{
//    protected $title;
    protected $detail;

//    protected $code;

    public function __construct($title, $detail = null, $code = 422)
    {
//        $this->message = $title;
        $this->detail = $title ?? $detail;
        $this->code = $code;
        parent::__construct($title, $code);

    }


    public function render($request)
    {
        $title = ($this->code == 422) ? trans('app.validation error') : trans('general.something went wrong');
        return response()->json([
            'errors' => [
                [
                    'title' => $title,
                    'detail' => $this->detail,
                    'status' => $this->code,
                ]
            ]
        ], $this->code);
    }
}
