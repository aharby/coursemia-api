<?php

namespace App\Modules\ContactUs\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Enums\StatusCodesEnum;

use App\Modules\ContactUs\Models\ContactUsForm;
use App\Mail\ContactMessageNotification;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function submit(Request $request)
    {
        $isGuest = !Auth::guard('api')->check();

        $rules = [
            'message' => 'required|string|max:2000',
        ];

        if ($isGuest) {
            $rules += [
                'name' => 'required|string|max:50',
                'email' => 'required|email|max:50',
                'phone' => 'nullable|string|max:20',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return customResponse((object)[], __($validator->errors()->first()), 422, StatusCodesEnum::FAILED);
        }

        $data = $validator->validated();
        $data['user_id'] = $isGuest ? null : Auth::guard('api')->id();

        ContactUsForm::create($data);

        $userEmail = $isGuest ? $request->input('email') : Auth::guard('api')->user()->email;

        Mail::to($userEmail)->send(new ContactMessageNotification($data));

        return customResponse([], __("api.Your message has been received."), 200, StatusCodesEnum::DONE);
    }
}
