<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Enums\SessionScoreTypesEnum;
use Illuminate\Validation\Rule;

class ScoreStudentResultsRequest extends BaseApiParserRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'attributes.session_score.*' => 'required|array|min:1',
            'attributes.session_score.*.score_type' => [
                'required',
                Rule::in(SessionScoreTypesEnum::getSessionScoreTypes()),
            ],
            'attributes.session_score.*.score' => 'required|integer'
        ];
        return $rules;
    }
}
