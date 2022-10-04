<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MatchingUseCase;


use App\OurEdu\Users\User;

interface FillMatchingUseCaseInterface
{
    public function fillResource(int $dataId,$data);
}
