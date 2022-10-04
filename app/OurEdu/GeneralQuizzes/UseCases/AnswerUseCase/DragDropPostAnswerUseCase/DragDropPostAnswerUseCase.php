<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\DragDropPostAnswerUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

class DragDropPostAnswerUseCase implements DragDropPostAnswerUseCaseInterface
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(GeneralQuizRepository $generalQuizRepository, GeneralQuizQuestionBank $questionBank,Collection $answers)
    {
        $answersData =[];
        $isCorrectQuestionArray = [];
        $question = $questionBank->question;

        $validationError = $this->validateAnswers($answers,$question);
        if($validationError){
            return $validationError;
        }
        // $this->validateAnswers($answers,$question);

        $isCorrectQuestion = false;
        $score=0;
        foreach ($answers as $answer) {
            $singleQuestionId = $answer->single_question_id;
            $isCorrect = $question->questions()->where('id', $singleQuestionId)->where('correct_option_id', $answer->answer_id)->exists();
            $isCorrectQuestionArray[] = $isCorrect;
            $answersData[] = [
                'option_id' => $answer->answer_id,
                'option_type'=> DragDropOption::Class,
                'is_correct_answer' => $isCorrect,
                'question_id' => $questionBank->id,
                'student_id'    =>  $this->user->id,
                'single_question_id' => $answer->single_question_id,
                'single_question_type'=> DragDropQuestion::class
            ];
        }
        if (in_array(false, $isCorrectQuestionArray, true) or ($question->questions()->count() != count($isCorrectQuestionArray)) ) {
            $isCorrectQuestion = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = true;
            $score=$questionBank->grade;
        }

        /*if (in_array(false, $isCorrectQuestionArray, true) or ($question->options->where('is_correct_answer',1)->count() != count($isCorrectQuestionArray))){
            $isCorrectQuestion = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = true;
            $score=$questionBank->grade;
        }*/
        $questionAnswer =  [
            'student_id'    =>  $this->user->id,
            'general_quiz_question_id'=>$questionBank->id,
            'general_quiz_id'    =>  $generalQuizRepository->generalQuiz->id,
            'single_question_id' => $question->id,
            'single_question_type'=> DragDropData::class,
            'answer_text'    =>  null,
            'is_correct'    =>  $isCorrectQuestion,
            'details' => $answersData,
            'score' => $score,
            'subject_format_subject_id'=>$questionBank->subject_format_subject_id
        ];
        return $questionAnswer;
    }



    protected function validateAnswers($answers,$question){
        if(count($answers) == 0){
            $return['status'] = 422;
            $return['detail'] = trans('general_quizzes.Please add at least one option');
            $return['title'] = 'Please add at least one option';
            return $return;
        }
        foreach($answers as $answer){
            if(!isset($answer->answer_id)){
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
}
