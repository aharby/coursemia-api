<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\TrueFalseUseCase;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\User;

interface FillTrueFalseUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data);
}
