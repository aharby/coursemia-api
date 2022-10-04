<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\HotSpotUseCase;

use App\OurEdu\Users\User;

interface FillHotSpotUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId, $data, User $user);
}
