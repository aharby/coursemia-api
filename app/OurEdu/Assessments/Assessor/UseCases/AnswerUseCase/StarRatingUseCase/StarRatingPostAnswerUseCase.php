<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\StarRatingUseCase;


use App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\RatingUseCase\RatingPostAnswerUseCase;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingOptions;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingQuestion;

class StarRatingPostAnswerUseCase extends RatingPostAnswerUseCase implements StarRatingPostAnswerUseCaseInterface
{
    protected function calculateScore(AssissmentRatingQuestion $question, AssissmentRatingOptions $answerOption): float
    {
        return $question->options
            ->where("order", "<=", $answerOption->order)
            ->sum('grade');
    }
}
