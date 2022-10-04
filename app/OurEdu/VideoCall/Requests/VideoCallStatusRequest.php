<?php

namespace App\OurEdu\VideoCall\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoCallStatusRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status'=>'required|in:accepted,rejected'
        ];
    }
}
