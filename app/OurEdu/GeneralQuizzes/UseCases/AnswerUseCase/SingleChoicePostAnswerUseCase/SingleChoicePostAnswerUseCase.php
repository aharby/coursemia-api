<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\SingleChoicePostAnswerUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

class SingleChoicePostAnswerUseCase implements SingleChoicePostAnswerUseCaseInterface
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank,Collection $answers)
    {
        $question = $questionBank->question;
        $validationError = $this->validateAnswers($answers,$question);

        if($validationError){
            return $validationError;
        }

        $type = $question->questionHead()->multipleChoiceType->type;
        $score=0;
        if ($answer = $answers->first()) {
            $option = $question->options->where('id', $answer->answer_id)->first();
            if($option->is_correct_answer){
                $score=$questionBank->grade;
            }
            return [
                'student_id'    =>  $this->user->id,
                'general_quiz_id'    =>  $generalQuizRepository->generalQuiz->id,
                'general_quiz_question_id'    =>  $questionBank->id,
                'single_question_id' => $question->id,
                'single_question_type'=>MultipleChoiceQuestion::class,
                'option_id'    =>   $option->id ?? null,
                'option_type'    =>  MultipleChoiceOption::class,
                'answer_text'    =>  null,
                'is_correct'    =>  $option->is_correct_answer ?? false,
                'score' => $score,
                'subject_format_subject_id'=>$questionBank->subject_format_subject_id
            ];
        }
    }


    protected function validateAnswers($answers,$question){
        if(count($answers)>1){
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.Please enter only one answer');
            $return['title'] = 'Please enter only one answer';
            return $return;
        }
        if(count($answers) == 0){
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.Please add at least one option');
            $return['title'] = 'Please add at least one option';
            return $return;
        }
        $answer = $answers->first();


        if(!isset($answer->answer_id) or $answer->answer_id == '' or  ctype_space( $answer->answer_id) ){
            $return['status'] = 422;
            $return['detail'] =  trans('general_quizzes.answer_id_required');
            $return['title'] = 'answer_id is required';
            return $return;
        }

        $questionOption = $question->options()
        ->where('id', $answer->answer_id)
        ->first();


        if(!$questionOption){
            $return['status'] = 422;
            $return['detail'] =  trans('general_quizzes.Invalid answer id',['id'=> $answer->answer_id]);
            $return['title'] = 'Invalid answer';
            return $return;
        }
    }
}
