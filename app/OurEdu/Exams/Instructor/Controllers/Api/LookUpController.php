<?php

namespace App\OurEdu\Exams\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Instructor\Transformers\LookUpTransformer;
use Illuminate\Http\Request;

class LookUpController extends BaseApiController
{

    public function index(Request $request)
    {
        $data = ['dum'=>'data'];

        $include = $request->get('include') ?? '';

        return $this->transformDataModInclude(
            $data,
            $include,
            new LookUpTransformer(auth()->user()),
            ResourceTypesEnums::LOOKUP
        );
    }
}
