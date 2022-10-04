<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\DragDropUseCase;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\User;

interface FillDragDropUseCaseUseCaseInterface
{
    public function fillResource(int $resourceSubjectFormatId,$data);
}
