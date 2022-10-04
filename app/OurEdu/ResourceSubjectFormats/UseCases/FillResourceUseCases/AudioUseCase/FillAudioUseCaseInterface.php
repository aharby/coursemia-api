<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\AudioUseCase;


use App\OurEdu\Users\User;

interface FillAudioUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user);
}
