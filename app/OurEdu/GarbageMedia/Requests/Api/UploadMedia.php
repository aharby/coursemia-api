<?php

namespace App\OurEdu\GarbageMedia\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class UploadMedia extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.media' => 'required|array',
            'attributes.media.*' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf,xls,csv,txt,xlsx,mp4,webm,wmv,avi,flv,swf,mpga,audio,mpeg,doc,docx,mp3,one',
        ];
    }
}
