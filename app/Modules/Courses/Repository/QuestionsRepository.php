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
        $category_ids = request()->category_ids;
        $number_of_questions = request()->number_of_questions;
        $questions = $this->model->query();
        if (count($category_ids) > 0){
            $questions = $questions->whereIn('category_id', $category_ids);
        }
       return $questions
            ->active()
            ->inRandomOrder()
            ->where('course_id', $courseId)
            ->with(['answers'])
            ->take($number_of_questions)
            ->get();
    }
}
