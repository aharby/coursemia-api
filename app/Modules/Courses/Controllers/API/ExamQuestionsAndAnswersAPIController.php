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
use App\Modules\Courses\Models\StudentQuestionAnswer;
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
use Illuminate\Support\Facades\Mail;
use App\Mail\ExamSubmittedNotification;

class ExamQuestionsAndAnswersAPIController extends Controller
{
    public function submitExamAnswers(SubmitExamAnswersRequest $request)
    {
        $course_id = $request->course_id;
        $category_id = $request->category_id;
        $answers = $request->answers;
        $correctAnswers = 0;
        $count = sizeof($answers);

        $user = auth('api')->user();

        // guest
        if (!isset($user)) {
            foreach ($answers as $answer){
                $myAnswer = Answer::find($answer['answer_id']);
                if ($myAnswer->is_correct)
                    $correctAnswers++;
            }
            $percentage = ($correctAnswers / $count)*100;

            return customResponse([
                'score_percentage' => $percentage
            ], trans('api.Your Answers have been submitted. Thank you!'), 200, StatusCodesEnum::DONE);
        }

        // authenticated user
        $emailData['questions'] = [];

        foreach ($answers as $answer) {
            $myAnswer = Answer::find($answer['answer_id']);
            $myAnswer->selection_count++;
            $myAnswer->save();
            
            StudentQuestionAnswer::create([
                'user_id' => auth('api')->user()->id,
                'question_id' => $myAnswer->question_id,
                'answer_id' => $myAnswer->id,
            ]);

            $isCorrect = $myAnswer->is_correct;

            if($isCorrect)
                $correctAnswers++;

            $question = Question::find($answer['question_id']);
            $questionData = [
            'title' => $question ? $question->title : null,
            'is_correctly_answered' => $isCorrect,
            'selected_answer' => $myAnswer ? $myAnswer->answer : null,
            ];

            if (!$isCorrect) {
                $correctAnswer = Answer::where('question_id', $question->id)
                    ->where('is_correct', 1)
                    ->first();
                $questionData['correct_answer'] = $correctAnswer ? $correctAnswer->answer : null;
            }

            $emailData['questions'][] = $questionData;
        }

        $percentage = ($correctAnswers / $count)*100;

        $emailData['score_percentage'] = $percentage;
        $emailData['user_name'] = $user->full_name;
        $emailData['submitted_at'] = now()->format('Y-m-d H:i:s');
        $emailData['correct_answers'] = $correctAnswers;
        $emailData['total_questions'] = $count;
        $emailData['course_title'] = Course::find($course_id)->getTitleAttribute();
        $emailData['passing_score'] = 50;
        
        Mail::to($user->email)->send(new ExamSubmittedNotification($emailData));

        return customResponse([
            'score_percentage' => $percentage
           ], trans('api.Your Answers have been submitted. Thank you!'),
            200, StatusCodesEnum::DONE);
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

