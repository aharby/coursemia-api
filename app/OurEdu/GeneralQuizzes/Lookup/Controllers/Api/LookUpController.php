<?php

namespace App\OurEdu\GeneralQuizzes\Lookup\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Lookup\Transformers\LookUpTransformer;
use Illuminate\Http\Request;

class LookUpController extends BaseApiController
{
    public function index(Request $request)
    {
        $data = ['dum'=>'data'];

        $include = $request->get('include') ?? '';
        if($request->get('include') == 'registration_lookups'){
            $include = "countries, educationalSystems, schools, classes, academicYear";
        }
        $param = $request->get('filter') ?? [];

        return $this->transformDataModInclude(
            $data,
            $include,
            new LookUpTransformer($param),
            ResourceTypesEnums::LOOKUP
        );
    }
}
