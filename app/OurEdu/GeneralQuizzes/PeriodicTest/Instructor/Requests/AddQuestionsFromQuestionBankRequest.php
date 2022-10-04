<?php
namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests;

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
