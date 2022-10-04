<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultiMatchingUseCase;


use App\OurEdu\Users\User;

interface FillMultiMatchingUseCaseInterface
{
    public function fillResource(int $dataId , $data);
}
