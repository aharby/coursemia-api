<?php

namespace App\Modules\Courses\Repository;

use App\Modules\Courses\Models\Question;

class QuestionsRepository implements QuestionsRepositoryInterface
{
    private $model;
    public function __construct(Question $question)
    {
        $this->model = $question;
    }

    public function getQuestionsByCourseId($courseId)
    {
       return $this->model->query()
            ->active()
            ->where('course_id', $courseId)
            ->with(['answers'])
            ->paginate(env('PAGE_LIMIT', 20));
    }
}
