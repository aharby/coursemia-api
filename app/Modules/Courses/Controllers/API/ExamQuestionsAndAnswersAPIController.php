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
use App\Modules\Courses\Models\FlashCardAnswer;
use App\Modules\Courses\Models\Question;
use App\Modules\Courses\Models\UserQuestionAnswer;
use App\Modules\Courses\Repository\QuestionsRepositoryInterface;
use App\Modules\Courses\Requests\Api\StorePostRequest;
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
    public function submitExamAnswers(SubmitExamAnswersRequest $request)
    {
        $course_id = $request->course_id;
        $category_id = $request->category_id;
        $answers = $request->answers;
        $correctAnswers = 0;
        $count = sizeof($answers);
        foreach ($answers as $answer){
//            $question = Question::find($answer['question_id']);
            if (isset($answer['answer_id'])){
                $myAnswer = Answer::find($answer['answer_id']);
                $myAnswer->selection_count++;
                $myAnswer->save();
                $userQuestionAnswer = UserQuestionAnswer::create([
                    'user_id' => auth('api')->user()->id,
                    'question_id' => $myAnswer->question_id,
                    'answer_id' => $myAnswer->id,
                ]);
                if ($myAnswer->is_correct){
                    $correctAnswers++;
                }
            }else{
                $userQuestionAnswer = UserQuestionAnswer::create([
                    'user_id' => auth('api')->user()->id,
                    'question_id' => $answer['question_id'],
                    'answer_id' => null,
                ]);
            }

        }
        $percentage = ($correctAnswers / $count)*100;
        return customResponse([
            'score_percentage' => $percentage
           ], trans('api.submit exam'), 200, StatusCodesEnum::DONE);
    }

    public function submitFlashCardAnswer(SubmitFlashCardAnswersRequest $request){
        $course_id = $request->course_id;
        $category_id = $request->category_id;
        $answers = $request->answers;
        $correctAnswers = 0;
        $count = sizeof($answers);
        $user = auth('api')->user();
        if (isset($user)){
            foreach ($answers as $answer){
                $myAnswer = FlashCardAnswer::where(['course_flashcard_id' => $answer['id'], 'user_id' => $user->id])
                    ->first();
                if (isset($myAnswer)){
                    if ($myAnswer->answer == true){
                        continue;
                    }
                    else{
                        if ($answer['answer'] == true){
                            $myAnswer->answer = 1;
                            $myAnswer->save();
                            $correctAnswers++;
                        }
                    }
                }else{
                    if ($answer['answer'] == true){
                        $correctAnswers++;
                        $myFlashCardAnswer = new FlashCardAnswer;
                        $myFlashCardAnswer->course_flashcard_id = $answer['id'];
                        $myFlashCardAnswer->user_id = $user->id;
                        $myFlashCardAnswer->answer = 1;
                        $myFlashCardAnswer->save();
                    }else{
                        $myFlashCardAnswer = new FlashCardAnswer;
                        $myFlashCardAnswer->course_flashcard_id = $answer['id'];
                        $myFlashCardAnswer->user_id = $user->id;
                        $myFlashCardAnswer->answer = 0;
                        $myFlashCardAnswer->save();
                    }
                }
            }
        }else{
            foreach ($answers as $answer){
                if ($answer['answer'] == true){
                    $correctAnswers++;
                }
            }
        }
        return customResponse([
            'correct_answers' => $correctAnswers
           ] , trans('api.submit flashcard'), 200, StatusCodesEnum::DONE);
    }
}

