<?php


namespace App\OurEdu\Subjects\UseCases\PullTaskUseCase;


use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\Repository\ContentAuthorRepositoryInterface;
use App\OurEdu\Users\User;

interface PullTaskUseCaseInterface
{
    function pullTask(int $taskId,User $user);
}
