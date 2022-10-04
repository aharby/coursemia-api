<?php

namespace App\OurEdu\Subjects\UseCases\MarkTaskAsDoneUseCase;

interface MarkTaskAsDoneUseCaseInterface
{
    public function markTaskAsDone(int $taskId);
}
