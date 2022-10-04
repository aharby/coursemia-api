<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MultipleChoiceUseCase;

use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceOptions;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;

class MultipleChoicePostAnswerUseCase implements MultipleChoicePostAnswerUseCaseInterface
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(AssessmentRepository $assessmentRepository, AssessmentQuestion $assessmentQuestion,Collection $answers,$questionAnswerData)
    {

        $answersData =[];
        $question = $assessmentQuestion->question;
        $score=0;

        if ($questionAnswerData->question_type == QuestionTypesEnums::SINGLE_CHOICE) {
            $error = $this->validateMCQSingleChoicePostAnswer($answers,$question);
            if ($error) {
                return $error;
            }
            if ($answer = $answers->first()) {
                $option = $question->options->where('id', $answer->answer_id)->first();
                $score = $option->grade;
                $answersData[] = [
                    'option_id' => $answer->answer_id,
                    'option_type'=> AssessmentMultipleChoiceOptions::class,
                    'assessment_question_id' => $assessmentQuestion->id,
                    'user_id'    =>  $this->user->id,
                    'score'=>$option->grade
                ];
            }
        }

        if ($questionAnswerData->question_type == QuestionTypesEnums::MULTI_CHOICE) {
            $error = $this->validateMultipleChoicesPostAnswer($answers,$question);
            if ($error) {
                return $error;
            }
            foreach ($answers as $answer) {
                $selected = $question->options
                    ->where('id', $answer->answer_id)
                    ->first();
                $score += $selected->grade;
                $answersData[] = [
                    'option_id' => $answer->answer_id,
                    'option_type'=> AssessmentMultipleChoiceOptions::class,
                    'assessment_question_id' => $assessmentQuestion->id,
                    'user_id'    =>  $this->user->id,
                    'score'=>$selected->grade
                ];
            }
            if($score > $assessmentQuestion->question_grade){
                $score = $assessmentQuestion->question_grade;
            }
        }


        $questionAnswer =  [
            'user_id'    =>  $this->user->id,
            'assessment_id'    =>  $assessmentRepository->assessment->id,
            'assessment_question_id'    =>  $assessmentQuestion->id,
            'score' => $score,
            'details'=>$answersData
        ];
        return $questionAnswer;
    }



    protected function validateMultipleChoicesPostAnswer($answers,$question){
        if(count($answers)<2){
            $return['status'] = 422;
            $return['detail'] = trans('assessment.Please add at least two option');
            $return['title'] = 'Please add at least two option';
            return $return;
        }
        foreach($answers as $answer){
            if(!isset($answer->answer_id) or $answer->answer_id == '' or  ctype_space( $answer->answer_id) ){
                $return['status'] = 422;
                $return['detail'] =  trans('assessment.answer_id_required');
                $return['title'] = 'answer_id is required';
                return $return;
            }

            $questionOption = $question->options
            ->where('id', $answer->answer_id)
            ->first();


            if(!$questionOption){
                $return['status'] = 422;
                $return['detail'] =  trans('assessment.you have to select one option');
                $return['title'] = 'Invalid answer';
                return $return;
            }
        }
    }


    protected function validateMCQSingleChoicePostAnswer($answers,$question){
        if(count($answers)>1){
            $return['status'] = 422;
            $return['detail'] = trans('assessment.Please enter only one answer');
            $return['title'] = 'Please enter only one answer';
            return $return;
        }
        if(count($answers) == 0){
            $return['status'] = 422;
            $return['detail'] = trans('assessment.Please add at least one option');
            $return['title'] = 'Please add at least one option';
            return $return;
        }
        $answer = $answers->first();


        if(!isset($answer->answer_id) or $answer->answer_id == '' or  ctype_space( $answer->answer_id) ){
            $return['status'] = 422;
            $return['detail'] =  trans('assessment.answer_id_required');
            $return['title'] = 'answer_id is required';
            return $return;
        }

        $questionOption = $question->options
            ->where('id', $answer->answer_id)
            ->first();


        if(!$questionOption){
            $return['status'] = 422;
            $return['detail'] =  trans('assessment.you have to select one option');
            $return['title'] = 'Invalid answer';
            return $return;
        }
    }

}
