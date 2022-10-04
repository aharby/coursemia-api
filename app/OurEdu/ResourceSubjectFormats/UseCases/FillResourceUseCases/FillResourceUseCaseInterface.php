<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\User;

interface FillResourceUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data, User $user);
}
