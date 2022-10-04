<?php

namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayQuestion;
use Illuminate\Support\Facades\DB;

class GeneralQuizStudentAnswerRepository implements GeneralQuizStudentAnswerRepositoryInterface
{

    public function findOrFail($answerId)
    {
        return GeneralQuizStudentAnswer::findOrFail($answerId);
    }

    public function update($answer, $data)
    {
        return $answer->update($data);
    }

    public function hasUnReviewedEssayQuestions($homework_id)
    {
        $count = GeneralQuizStudentAnswer::where('general_quiz_id', '=', $homework_id)
            ->where('single_question_type', '=', EssayQuestion::class)
            ->where('is_reviewed', '=', 0)->pluck('id')->count();

        return $count > 0 ? true : false;
    }

}
