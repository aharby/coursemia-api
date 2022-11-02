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
        $number_of_questions = request()->exam_content;
        $questions = $this->model->query();
        if (isset($category_ids)){
            $questions = $questions->whereIn('category_id', $category_ids);
        }
        // Timed test so we have to get all questions
        if (request()->exam_type == 2){
            return $questions
                ->active()
                ->inRandomOrder()
                ->where('course_id', $courseId)
                ->with(['answers'])
                ->get();
        }
        // Question bank so we have to get certain number of questions
       return $questions
            ->active()
            ->inRandomOrder()
            ->where('course_id', $courseId)
            ->with(['answers'])
            ->take($number_of_questions)
            ->get();
    }
}
