<?php

namespace App\OurEdu\Assessments\AssessmentManager\Controllers\Api;

use App\OurEdu\Assessments\AssessmentManager\Requests\CreateCategoryRequest;
use App\OurEdu\Assessments\AssessmentManager\Requests\UpdateCategoryRequest;
use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentCategoryTransformer;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentCategory;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentCategoryRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class AssessmentsCategoryController extends BaseApiController
{

    public function __construct(private AssessmentCategoryRepositoryInterface $assessmentCategoryRepository , private  ParserInterface $parserInterface)
    {
        $this->middleware('type:assessment_manager');
    }

    public function index(Assessment $assessment)
    {
        return $this->transformDataMod($assessment->categories, new AssessmentCategoryTransformer(), ResourceTypesEnums::ASSESSMENT_CATEGORY);
    }

    public function create(CreateCategoryRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $parseData = [];
        $parseData['title'] = $data['attributes']['title'];
        $parseData['assessment_id'] = $data['attributes']['assessment_id'];

        $category = $this->assessmentCategoryRepository->create($parseData)->fresh();

        return $this->transformDataMod($category, new AssessmentCategoryTransformer(), ResourceTypesEnums::ASSESSMENT_CATEGORY);
    }

    public function edit(UpdateCategoryRequest $request,AssessmentCategory $assessmentCategory)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $parseData = [];
        $parseData['title'] = $data['attributes']['title'];

      $this->assessmentCategoryRepository->update($assessmentCategory,$parseData);

        return $this->transformDataMod($assessmentCategory, new AssessmentCategoryTransformer(), ResourceTypesEnums::ASSESSMENT_CATEGORY);
    }

    public function delete(AssessmentCategory $assessmentCategory)
    {
        if($assessmentCategory->questions->count() > 0){

            return formatErrorValidation(
                [
                    'status'=>422,
                    'title' => trans('assessment.cannot be deleted has questions'),
                    'detail' => trans('assessment.cannot be deleted has questions'),
                ],
                422
            );
        }

        $this->assessmentCategoryRepository->delete($assessmentCategory);

        return response()->json(['meta' => [
            'message' => trans('app.Delete successfully')
        ]],200);

    }

}
