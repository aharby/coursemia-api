<?php


namespace App\OurEdu\Subjects\UseCases\ReleaseTaskUseCase;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\User;

interface ReleaseTaskUseCaseInterface
{
    function releaseTask(int $taskId,User $user);
}
