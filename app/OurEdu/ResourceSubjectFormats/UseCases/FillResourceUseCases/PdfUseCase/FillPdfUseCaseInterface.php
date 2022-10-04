<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PdfUseCase;


use App\OurEdu\Users\User;

interface FillPdfUseCaseInterface
{
    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     * @return mixed
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user);
}
