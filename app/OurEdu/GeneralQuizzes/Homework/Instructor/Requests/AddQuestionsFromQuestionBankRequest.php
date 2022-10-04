<?php
namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class AddQuestionsFromQuestionBankRequest extends BaseApiParserRequest
{

    public function rules()
    {
        return [
         'attributes.questions' => 'required'
        ];
    }
}
