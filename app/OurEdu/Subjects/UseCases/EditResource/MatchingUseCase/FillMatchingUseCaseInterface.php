<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\MatchingUseCase;


use App\OurEdu\Users\User;

interface FillMatchingUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data);
}
