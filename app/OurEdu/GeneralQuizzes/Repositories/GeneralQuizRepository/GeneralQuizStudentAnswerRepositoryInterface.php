<?php

namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;

interface GeneralQuizStudentAnswerRepositoryInterface
{
    public function update($answer , $data);
    public function findOrFail($answerId);
    public function hasUnReviewedEssayQuestions($homework_id);

}
