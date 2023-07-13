<?php

namespace App\Modules\Settings\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Setting;

class SettingsApiController extends Controller
{
    public function aboutUs()
    {
        return $this->getContent('about_us');
    }

    public function termsAndConditions()
    {
        return $this->getContent('terms_and_conditions');
    }

    public function privacyPolicy()
    {
        return $this->getContent('privacy_policy');
    }

    public function getContent($key)
    {
        $settings = Setting::where('key', $key)->first();
        return customResponse([
            'content'    => $settings->value
        ], "Done", 200, StatusCodesEnum::DONE);
    }
}
