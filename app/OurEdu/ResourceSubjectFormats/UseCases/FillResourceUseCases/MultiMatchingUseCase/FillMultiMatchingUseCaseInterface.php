<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultiMatchingUseCase;


use App\OurEdu\Users\User;

interface FillMultiMatchingUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data, User $user);
}
