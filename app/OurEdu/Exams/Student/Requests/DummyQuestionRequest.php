<?php

namespace App\OurEdu\Exams\Student\Requests;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Options\Option;
use http\Env\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Exams\Enums\ExamEnums;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class DummyQuestionRequest extends BaseApiParserRequest
{
    protected $routeParametersToValidate = ['type' => 'type'];

    public function rules()
    {
        return [
            'type' => [Rule::in(LearningResourcesEnums::getQuestionLearningResources())],
            ];
    }

    public function validationData():array
    {
        $data = parent::validationData();
        foreach ($this->routeParametersToValidate as $validationDataKey => $routeParameter) {
            $data[$validationDataKey] = $this->route($routeParameter);
        }

        return $data;
    }

}
