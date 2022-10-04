<?php


namespace App\OurEdu\VCRSessions\General\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class UploadVCRFileRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'file' => 'required|file|max:10240',
//            'file.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
