<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\App;
use Throwable;

class CustomErrorException extends Exception
{

public function __construct($message = "", $code = 500, Throwable $previous = null)
{
    parent::__construct($message, $code, $previous);
}

    public function render($request)
    {
        if (env('APP_DEBUG') == true) {

            $line = $this->getLine();
            $title = $this->getMessage();
            $detail = $this->getTrace();
            $file = $this->getFile();

            if ($request->wantsJson()) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => $this->getCode(),
                            'title' => $file,
                            'detail' => $file
                        ],
                        [
                            'status' => $this->getCode(),
                            'title' => $title,
                            'detail' => $detail
                        ],
                        [
                            'status' => $this->getCode(),
                            'title' => $line,
                            'detail' => $detail
                        ]
                    ]
                ], $this->getCode());
            }
        }

        if ($request->wantsJson()) {
            $title = trans('app.Oopps Something is broken');
            $detail = trans('app.Oopps Something is broken');
            return response()->json([
                'errors' => [
                    [
                        'status' => $this->getCode(),
                        'title' => $title,
                        'detail' => $detail
                    ]]
            ], $this->getCode());
        }

    }
}
