<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\MultiMatchingUseCase;


use App\OurEdu\Users\User;

interface FillMultiMatchingUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data);
}
