<?php

namespace App\Modules\Courses\Repository;

interface QuestionsRepositoryInterface
{
    public function getQuestionsByCourseId($courseId);
}
