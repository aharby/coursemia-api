<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PictureUseCase;


use App\OurEdu\Users\User;

interface FillPictureUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user);
}
