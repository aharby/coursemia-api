<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\EssayPostAnswerUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

class EssayPostAnswerUseCase implements EssayPostAnswerUseCaseInterface
{
    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank, Collection $answers)
    {
        $validationErrors = $this->checkValidation($questionBank,$answers);
        if ($validationErrors) {
            return $validationErrors;
        }
        $question = $questionBank->question;
        if ($answer = $answers->first()) {
            return [
                'student_id' => $this->user->id,
                'general_quiz_question_id' => $questionBank->id,
                'general_quiz_id'    =>  $generalQuizRepository->generalQuiz->id,
                'single_question_id' => $question->id,
                'single_question_type' => EssayQuestion::class,
                'answer_text' => $answer->answer_text,
                'is_reviewed' => false,
                'subject_format_subject_id'=>$questionBank->subject_format_subject_id
            ];
        }
        throw new ErrorResponseException("api.Unable to define answer");
    }

    public function checkValidation($questionBank,$answers)
    {
        if (count($answers) > 1) {
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.Please enter only one answer');
            $return['title'] = 'Please enter only one answer';
            return $return;
        }

        if($questionBank->question_type != EssayQuestion::class){
            $return['status'] = 422;
            $return['detail'] =  trans('general_quizzes.Invalid Question Type');
            $return['title'] = 'This question is not essay question';
            return $return;
        }
        $answer = $answers->first();
        if(!isset($answer->answer_text)|| $answer->answer_text == '' || ctype_space($answer->answer_text) ){
            $return['status'] = 422;
            $return['title'] = 'answer text is required';
            $return['detail'] = trans('general_quizzes.answer text is required');
            return $return;
        }
    }
}
