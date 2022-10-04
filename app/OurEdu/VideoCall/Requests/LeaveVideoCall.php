<?php

namespace App\OurEdu\VideoCall\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Illuminate\Foundation\Http\FormRequest;

class LeaveVideoCall extends BaseApiParserRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.video_call_request'=>'required|exists:video_call_requests,id'
        ];
    }
}
