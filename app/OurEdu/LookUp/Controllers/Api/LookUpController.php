<?php

namespace App\OurEdu\LookUp\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LookUp\Transformers\LookUpTransformer;
use Illuminate\Http\Request;

class LookUpController extends BaseApiController
{
    public function __construct()
    {
    }

    public function getIndex(Request $request)
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
