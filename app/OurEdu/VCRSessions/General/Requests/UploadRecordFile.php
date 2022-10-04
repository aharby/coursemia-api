<?php


namespace App\OurEdu\VCRSessions\General\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class UploadRecordFile extends BaseApiParserRequest
{
    public function rules()
    {
        return [
//            'file' => 'required|file|mimes:mp4,ogx,oga,ogv,ogg,webm,part',
        ];
    }
}
