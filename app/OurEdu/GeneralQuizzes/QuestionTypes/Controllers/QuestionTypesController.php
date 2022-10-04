<?php

namespace App\OurEdu\GeneralQuizzes\QuestionTypes\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\QuestionTypes\Transformers\QuestionTypesTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

class QuestionTypesController extends BaseApiController
{
    public function index(GeneralQuiz $generalQuiz)
    {
        $types = QuestionsTypesEnums::List();

        if ($generalQuiz->quiz_type == GeneralQuizTypeEnum::FORMATIVE_TEST) {
            $types = QuestionsTypesEnums::formativeTestList();
        }


        return $this->transformDataMod(
            [
               $types
            ],
            new QuestionTypesTransformer(),
            ResourceTypesEnums::QUESTION_TYPES
        );
    }
}
