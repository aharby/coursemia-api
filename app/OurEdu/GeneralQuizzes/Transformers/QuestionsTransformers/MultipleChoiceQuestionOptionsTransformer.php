<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers;

use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Users\UserEnums;

class MultipleChoiceQuestionOptionsTransformer extends TransformerAbstract
{
    public function transform(MultipleChoiceOption $option)
    {
        $transformedData = [
            "id" => (int)$option->id,
            "option" => (string)$option->answer,
        ];

        if (
            in_array(
                auth()->user()->type,
                [
                    UserEnums::SCHOOL_SUPERVISOR,
                    UserEnums::SCHOOL_LEADER,
                    UserEnums::EDUCATIONAL_SUPERVISOR,
                    UserEnums::ACADEMIC_COORDINATOR,
                    UserEnums::SCHOOL_INSTRUCTOR,
                    UserEnums::INSTRUCTOR_TYPE,
                    UserEnums::SCHOOL_ADMIN,
                    UserEnums::PARENT_TYPE
                ]
            )
        ) {
            $transformedData["is_correct_answer"] = (bool)$option->is_correct_answer;
        }
        return $transformedData;
    }
}
