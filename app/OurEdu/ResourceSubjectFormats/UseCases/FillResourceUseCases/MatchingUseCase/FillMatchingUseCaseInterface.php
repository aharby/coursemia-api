<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MatchingUseCase;


use App\OurEdu\Users\User;

interface FillMatchingUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data, User $user);
}
