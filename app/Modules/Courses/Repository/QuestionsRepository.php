<?php

namespace App\Modules\Courses\Repository;

use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\CourseUser;
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
        $user = auth('api')->user();
        $isMyCourse = 0;
        if (isset($user))
            $isMyCourse = CourseUser::where(['course_id' => $courseId, 'user_id' => $user->id])->count();
        $category_ids = request()->category_ids;
        $sub_category = request()->sub_category_ids;
        $number_of_questions = request()->number_of_questions;
        $questions = $this->model->query();
        if (!empty($category_ids)){
            $sub_categories_of_category_ids = Category::whereIn('parent_id', $category_ids )->pluck('id')->toArray();
            $questions = $questions->whereIn('category_id', array_merge($category_ids, $sub_categories_of_category_ids));
        }
        if (!empty($sub_category)){
            $questions = $questions->whereIn('category_id', $sub_category);
        }
        if ($isMyCourse < 1)
            $questions = $questions->where('is_free_content' , '=', 1);
        // Timed test so we have to get all questions
        if (request()->exam_type == 2){
            return $questions
                ->active()
                ->filter()
                ->inRandomOrder()
                ->where('course_id', $courseId)
                ->with(['answers' => function ($answers) {
                    $answers->inRandomOrder();
                }])
                ->get();
        }
        // Question bank so we have to get certain number of questions
       return $questions
            ->active()
            ->filter()
            ->inRandomOrder()
            ->where('course_id', $courseId)
            ->with(['answers' => function ($answers) {
               $answers->inRandomOrder();
            }])
            ->take($number_of_questions)
            ->get();
    }
}
