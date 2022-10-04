<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FlashUseCase;


use App\OurEdu\Users\User;

interface FillFlashUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user);
}
