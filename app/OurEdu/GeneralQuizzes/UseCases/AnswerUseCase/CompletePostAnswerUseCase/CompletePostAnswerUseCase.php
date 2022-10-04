<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\CompletePostAnswerUseCase;

use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

class CompletePostAnswerUseCase implements CompletePostAnswerUseCaseInterface
{
    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank, Collection $answers)
    {
        $validationErrors = $this->checkValidation($answers,$questionBank);
        if ($validationErrors) {
            return $validationErrors;
        }
        $question = $questionBank->question;
        if ($answer = $answers->first()) {

            $answerText = trim($answer->answer_text);

            $isCorrect = $this->checkCorrectAnswer($question, $answerText) ? true:false;

            $score = 0;
            if($isCorrect){
                $score=$questionBank->grade;
            }

            return [
                'student_id' => $this->user->id,
                'general_quiz_question_id' => $questionBank->id,
                'general_quiz_id'    =>  $generalQuizRepository->generalQuiz->id,
                'single_question_id' => $question->id,
                'single_question_type' => CompleteQuestion::class,
                'answer_text' => $answerText,
                'is_correct'=>$isCorrect,
                'score' => $score,
                'subject_format_subject_id'=>$questionBank->subject_format_subject_id
            ];
        }
        throw new ErrorResponseException("api.Unable to define answer");
    }

    public function checkValidation($answers,$questionBank)
    {
        if($questionBank->question_type != CompleteQuestion::class){
            $return['status'] = 422;
            $return['detail'] =  trans('general_quizzes.Invalid Question Type');
            $return['title'] = 'This question is not Complete question';
            return $return;
        }

        if (count($answers) > 1) {
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.Please enter only one answer');
            $return['title'] = 'Please enter only one answer';
            return $return;
        }
        $answer = $answers->first();
        if(!isset($answer->answer_text)|| $answer->answer_text == '' || ctype_space($answer->answer_text) ){
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.answer text is required');
            $return['title'] = 'answer text is required';
            return $return;
        }
    }

    public function checkCorrectAnswer($mainQuestion, $answerText, $answerId = null)
    {
        return $mainQuestion->answer()->where('answer', $answerText)->first() ?? $mainQuestion->acceptedAnswers()->where('answer', $answerText)->first();
    }
}
