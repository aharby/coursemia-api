<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\TrueFalseUseCase;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\User;

interface FillTrueFalseUseCaseInterface
{
    public function fillResource(int $questionId,$data);
}
