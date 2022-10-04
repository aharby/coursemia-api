<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PageUseCase;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\User;

interface FillPageUseCaseUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data, User $user);
}
