<?php

namespace App\OurEdu\Subjects\UseCases\EditResource\HotSpotUseCase;

use App\OurEdu\Users\User;

interface FillHotSpotUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId, $data);
}
