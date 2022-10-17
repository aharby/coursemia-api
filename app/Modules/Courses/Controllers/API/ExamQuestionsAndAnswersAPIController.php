<?php

namespace App\Modules\Courses\Controllers\API;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Countries\Resources\Api\ListCountriesIndexPaginator;
use App\Modules\Courses\Models\Answer;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseFlashcard;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Models\Question;
use App\Modules\Courses\Models\UserQuestionAnswer;
use App\Modules\Courses\Repository\QuestionsRepositoryInterface;
use App\Modules\Courses\Requests\Api\SubmitExamAnswersRequest;
use App\Modules\Courses\Requests\Api\SubmitFlashCardAnswersRequest;
use App\Modules\Courses\Resources\API\CourseDetailsResource;
use App\Modules\Courses\Resources\API\CourseLectureResource;
use App\Modules\Courses\Resources\API\CourseNoteResource;
use App\Modules\Courses\Resources\API\CoursesCollection;
use App\Modules\Courses\Resources\API\CoursesResource;
use App\Modules\Courses\Resources\API\FlashCardsResource;
use App\Modules\Courses\Resources\Api\ListCourseQuestionsPaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamQuestionsAndAnswersAPIController extends Controller
{
    public function getCourseFlashCards(SubmitExamAnswersRequest $request)
    {
        $course_id = $request->course_id;
        $category_id = $request->category_id;
        $answers = $request->answers;
        $correctAnswers = 0;
        $count = sizeof($answers);
        foreach ($answers as $answer){
//            $question = Question::find($answer['question_id']);
            $myAnswer = Answer::find($answer['answer_id']);
            $myAnswer->selection_count++;
            $myAnswer->save();
            $userQuestionAnswer = UserQuestionAnswer::create([
                'user_id' => $request->user()->id,
                'question_id' => $myAnswer->question_id,
                'answer_id' => $myAnswer->id,
            ]);
            if ($myAnswer->is_correct){
                $correctAnswers++;
            }
        }
        $percentage = ($correctAnswers / $count)*100;
        return customResponse($percentage, trans('api.submit exam'), 200, StatusCodesEnum::DONE);
    }

    public function submitFlashCardAnswer(SubmitFlashCardAnswersRequest $request){
        $course_id = $request->course_id;
        $category_id = $request->category_id;
        $answers = $request->answers;
        $correctAnswers = 0;
        $count = sizeof($answers);
        foreach ($answers as $answer){
            $myAnswer = CourseFlashcard::find($answer['flashcard_id']);
            if ($myAnswer->answer == $answer['answer']){
                $correctAnswers++;
            }
        }
        $percentage = ($correctAnswers / $count)*100;
        return customResponse(round($percentage,1), trans('api.submit flashcard'), 200, StatusCodesEnum::DONE);
    }
}
