<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\VideoUseCase;


use App\OurEdu\Users\User;

interface FillVideoUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user);
}