<?php


namespace App\OurEdu\Assessments\AssessmentManager\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class AssessmentPointsRateRequest extends BaseApiParserRequest
{


    public function rules()
    {
        return [
        //    'relationship.rates.*.min_points' => 'required|integer',
        //    'relationship.rates.*.max_points' => 'required|integer|gt:attributes.rates.*.min_points',
        //    'relationship.rates.*.rate' => 'required|string|distinct:ignore_case',
        ];
    }
}
