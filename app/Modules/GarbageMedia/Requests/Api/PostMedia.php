<?php

namespace App\Modules\GarbageMedia\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PostMedia extends FormRequest
{
    public function rules()
    {
        return [
            'media' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf,xls,csv,txt,xlsx,mp4,webm,wmv,avi,flv,swf,mpga,audio,mpeg,doc,docx,mp3,one|max:5000',
        ];
    }
}
