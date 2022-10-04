<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Requests;

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
            'attributes.score' => 'required|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'attributes.score' => trans('general_quizzes.score')
        ];
    }
}
