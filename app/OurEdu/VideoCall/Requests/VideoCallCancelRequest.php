<?php

namespace App\OurEdu\VideoCall\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Illuminate\Foundation\Http\FormRequest;

class VideoCallCancelRequest extends BaseApiParserRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.request_id'=>'required|exists:video_call_requests,id'
        ];
    }
}
