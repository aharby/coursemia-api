<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

class TrueFalsePostAnswerUseCase implements TrueFalsePostAnswerUseCaseInterface
{
    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank, Collection $answers)
    {
        $validationErrors = $this->checkValidation($answers);
        if ($validationErrors) {
            return $validationErrors;
        }
        $question = $questionBank->question;
        $type = $question->questionHead()->TrueFalseType->type;
        $score = 0;
        if ($answer = $answers->first()) {
            $isCorrect = $question->is_true == $answer->answer_text ? true : false;
            if($isCorrect){
                $score=$questionBank->grade;
            }
            return [
                'student_id' => $this->user->id,
                'general_quiz_question_id' => $questionBank->id,
                'general_quiz_id'    =>  $generalQuizRepository->generalQuiz->id,
                'single_question_id' => $question->id,
                'single_question_type' => TrueFalseQuestion::class,
                'answer_text' => $answer->answer_text,
                'is_correct' => $isCorrect,
                'score' => $score,
                'subject_format_subject_id'=>$questionBank->subject_format_subject_id
            ];
        }
        throw new ErrorResponseException("api.Unable to define answer");
    }

    public function checkValidation($answers)
    {
        if (count($answers) > 1) {
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.Please enter only one answer');
            $return['title'] = 'Please enter only one answer';
            return $return;
        }
        $answer = $answers->first();
        if(!isset($answer->answer_text) || !is_bool($answer->answer_text) ){
            $return['status'] = 422;
            $return['title'] = trans('general_quizzes.answer text is required in boolean');
            $return['detail'] = 'answer text is required in boolean';
            return $return;
        }
    }
}
