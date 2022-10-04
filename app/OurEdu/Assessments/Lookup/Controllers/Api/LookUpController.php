<?php

namespace App\OurEdu\Assessments\Lookup\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Lookup\Transformers\LookUpTransformer;
use Illuminate\Http\Request;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Lookup\Transformers\AssessmentTransformer;

class LookUpController extends BaseApiController
{
    public function index(Request $request)
    {
        $data = ['dum' => 'data'];

        // $include = $request->get('include') ?? 'users';
        $param = $request->get('filter') ?? [];
        return $this->transformDataModInclude(
            $data,
            'users',
            new LookUpTransformer($param),
            ResourceTypesEnums::LOOKUP
        );
    }

    public function getAssesments()
    {
        $assesments = Assessment::query()->select('id', 'title')
            ->where('created_by', auth()->id())
            ->where('start_at', '<=', now())
            ->orderByDesc("start_at")
            ->get();

        return $this->transformDataModInclude(
            $assesments,
            '',
            new AssessmentTransformer(),
            ResourceTypesEnums::ASSESSMENT
        );
    }
}
