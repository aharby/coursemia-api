<?php

namespace App\Modules\FAQs\Controllers;

use App\Enums\StatusCodesEnum;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Countries\Resources\Api\FAQResource;
use App\Modules\Countries\Resources\Api\FAQCollection;

class FAQApiController
{
    public function __construct(
        public CountryRepositoryInterface $countryRepository
    )
    {
    }

    public function index()
    {
        $countries = Country::query()->active();
        $countries = $countries->get();
        return customResponse(FAQResource::collection($countries), __('Done'), 200, StatusCodesEnum::DONE);
    }
}
