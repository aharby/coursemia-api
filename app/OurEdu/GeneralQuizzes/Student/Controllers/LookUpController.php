<?php

namespace App\OurEdu\GeneralQuizzes\Student\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Student\Transformers\LookUpTransformer;
use Illuminate\Http\Request;

class LookUpController extends BaseApiController
{
    public function lookUp(Request $request)
    {
        $data = ['dum'=>'data'];

        $include = $request->get('include') ?? '';

        return $this->transformDataModInclude(
            $data,
            $include,
            new LookUpTransformer(),
            ResourceTypesEnums::LOOKUP
        );
    }
}
