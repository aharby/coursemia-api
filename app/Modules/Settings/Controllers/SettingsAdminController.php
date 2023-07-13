<?php

namespace App\Modules\Settings\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Setting;

class SettingsAdminController extends Controller
{
    public function getAboutUs()
    {
        return $this->getContent('about_us');
    }

    public function postAboutUs()
    {
        $setting = Setting::where('key', 'about_us')->first();
        $setting->value = request()->get('content');
        $setting->save();
        return customResponse((object)[], 'done', 200, StatusCodesEnum::DONE);
    }

    public function getTermsAndConditions()
    {
        return $this->getContent('terms_and_conditions');
    }

    public function postTermsAndConditions()
    {
        $setting = Setting::where('key', 'terms_and_conditions')->first();
        $setting->value = request()->get('content');
        $setting->save();
        return customResponse((object)[], 'done', 200, StatusCodesEnum::DONE);
    }

    public function getPrivacyPolicy()
    {
        return $this->getContent('privacy_policy');
    }

    public function postPrivacyPolicy()
    {
        $setting = Setting::where('key', 'privacy_policy')->first();
        $setting->value = request()->get('content');
        $setting->save();
        return customResponse((object)[], 'done', 200, StatusCodesEnum::DONE);
    }

    public function getContent($key)
    {
        $settings = Setting::where('key', $key)->first();
        return customResponse([
            'content'    => $settings->value
        ], "Done", 200, StatusCodesEnum::DONE);
    }
}
