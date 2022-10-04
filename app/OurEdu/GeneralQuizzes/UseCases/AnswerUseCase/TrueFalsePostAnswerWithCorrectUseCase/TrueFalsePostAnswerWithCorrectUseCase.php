<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\TrueFalsePostAnswerWithCorrectUseCase;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
class TrueFalsePostAnswerWithCorrectUseCase implements TrueFalsePostAnswerWithCorrectUseCaseInterface
{

    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank,Collection $answers)
    {
        $question = $questionBank->question;
        $type = $question->questionHead()->TrueFalseType->type;
        $validationError = $this->validateAnswers($answers,$question);
        $score=0;
        if($validationError){
            return $validationError;
        }
        $this->validateAnswers($answers,$question);
        $answer = $answers->first();
        if ($question->is_true) {
            $isCorrect = $question->is_true == $answer->answer_text ? true : false;
            if($isCorrect){
                $score=$questionBank->grade;
            }
            return [
                'student_id'    =>  $this->user->id,
                'general_quiz_question_id'=>$questionBank->id,
                'general_quiz_id'    =>  $generalQuizRepository->generalQuiz->id,
                'single_question_id' => $question->id,
                'single_question_type'=> TrueFalseQuestion::class,
                'answer_text'    =>  $answer->answer_text,
                'is_correct'    =>  $isCorrect,
                'score' => $score,
                'subject_format_subject_id'=>$questionBank->subject_format_subject_id
            ];

        }else{
            $answerData = [
                'student_id'    =>  $this->user->id,
                'general_quiz_question_id'=>$questionBank->id,
                'general_quiz_id'    =>  $generalQuizRepository->generalQuiz->id,
                'single_question_id' => $question->id,
                'single_question_type'=> TrueFalseQuestion::class,
                'answer_text'    =>  $answer->answer_text,
                'score' => $score,
                'subject_format_subject_id'=>$questionBank->subject_format_subject_id
            ];


            //  if student answer it by false  -> correct answer
            if(!$answer->answer_text){
                $answersData = [];
                $isCorrectQuestionArray = [];
                $isCorrectQuestion = false;
                foreach ($answers as $answer) {
                    $questionOption = $question->options()
                        ->where('id', $answer->answer_id)
                        ->first();

                    $isCorrect = $questionOption ? ($questionOption->is_correct_answer?true:false) : false;
                    $isCorrectQuestionArray[] = $isCorrect;
                    if($isCorrect){
                        $score=$questionBank->grade;
                    }
                    $answersData[] = [
                        'option_id' => $answer->answer_id,
                        'option_type'=> TrueFalseOption::class,
                        'is_correct_answer' => $isCorrect,
                        'question_id' => $questionBank->id,
                        'student_id'    =>  $this->user->id,
                        'single_question_id' => $question->id,
                        'single_question_type'=>TrueFalseQuestion::class,
                        'score' => $score
                    ];
                }

                //check if there's wrong answer or count of right answers == count of given answers
                //  as he should select all right answers
                if (in_array(false, $isCorrectQuestionArray, true) or ($question->options->where('is_correct_answer',1)->count() != count($isCorrectQuestionArray))) {
                    $isCorrectQuestion = false;
                } elseif (in_array(true, $isCorrectQuestionArray, true)) {
                    $isCorrectQuestion = true;
                }
                $answerData['is_correct'] =  $isCorrectQuestion;
                $answerData['score'] =  $answerData['is_correct'] ? $questionBank->grade : 0;
                $answerData['details'] = $answersData;
            }else{
                $answerData['is_correct'] = false;
            }
            return $answerData;
        }
    }

    protected function validateAnswers($answers,$question){
        $countOptions = $question->options()->count();
        if(count($answers)>1 || count($answers) == 0){
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
        if(!$question->is_true && !$answer->answer_text){
            if(!isset($answer->answer_id) && $countOptions){
                $return['status'] = 422;
                $return['detail'] =  trans('general_quizzes.answer_id_required');
                $return['title'] = 'answer_id is required';
                return $return;
            }

            $questionOption = $question->options()
            ->where('id', $answer->answer_id)
            ->first();


            if(!$questionOption && $countOptions){
                $return['status'] = 422;
                $return['detail'] =  trans('general_quizzes.Invalid answer id',['id'=> $answer->answer_id]);
                $return['title'] = 'Invalid answer';
                return $return;
            }
        }
    }
}
