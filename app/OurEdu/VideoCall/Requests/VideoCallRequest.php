<?php

namespace App\OurEdu\VideoCall\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VideoCallRequest extends BaseApiParserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.user_id'=>'required|exists:users,id'
        ];
    }
}
