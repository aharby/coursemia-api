<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class ReviewEssayQuestionRequest extends BaseApiParserRequest
{
    /**
     * @var ParserInterface
     */

    public function rules()
    {
        return [
            'attributes' => 'required',
            'attributes.score' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'attributes.score' => trans('general_quizzes.score')
        ];
    }

}
