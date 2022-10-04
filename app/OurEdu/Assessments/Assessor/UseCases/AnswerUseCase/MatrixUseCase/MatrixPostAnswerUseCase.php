<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\MatrixUseCase;

use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceOptions;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixData;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixColumn;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixRow
;
class MatrixPostAnswerUseCase implements MatrixPostAnswerUseCaseInterface
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

        $error = $this->validateAnswers($answers,$question);
        if ($error) {
            return $error;
        }

        foreach ($answers as $answer) {
            $selected = $question->columns()
                ->where('id', $answer->column_id)
                ->first();
            $score += $selected->grade;
            $answersData[] = [
                'option_id' => $answer->column_id,
                'option_type'=> AssessmentMatrixColumn::class,
                'assessment_question_id' => $assessmentQuestion->id,
                'user_id'    =>  $this->user->id,
                'res_question_id' => $answer->row_id,
                'res_question_type'=>AssessmentMatrixRow::class,
                'score'=>$selected->grade
            ];
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



    protected function validateAnswers($answers,$question){

        if(count($answers) == 0 || $question->number_of_rows != count($answers)){
            $return['status'] = 422;
            $return['detail'] = trans('assessment.you must answer all rows questions inside matrix');
            $return['title'] = 'you must answer all rows questions inside matrix';
            return $return;
        }


        $questionColumns = $question->columns->pluck('id')->toArray();
        $questionRows = $question->rows->pluck('id')->toArray();

        foreach($answers as  $key => $answer){
            if(!isset($answer->row_id) or $answer->row_id == '' or  !is_numeric( $answer->row_id) ){
                $return['status'] = 422;
                $return['detail'] =  trans('assessment.field_required_of_value',[
                    'field'=>trans('assessment.rowId'),'num'=>$key+1
                ]);
                $return['title'] = 'row_id is required';
                return $return;
            }
            if(!isset($answer->column_id) or $answer->column_id == '' or  !is_numeric( $answer->column_id) ){
                $return['status'] = 422;
                $return['detail'] =  trans('assessment.field_required_of_value',[
                    'field'=>trans('assessment.columnId'),'num'=>$key+1
                ]);
                $return['title'] = 'column_id is required';
                return $return;
            }

            if(!in_array($answer->row_id,$questionRows)){
                $return['status'] = 422;
                $return['detail'] =  trans('assessment.Invalid row id',['id'=> $answer->row_id]);
                $return['title'] = 'Invalid row';
                return $return;
            }

            if(!in_array($answer->column_id,$questionColumns)){
                $return['status'] = 422;
                $return['detail'] =  trans('assessment.Invalid column id',['id'=> $answer->column_id]);
                $return['title'] = 'Invalid column';
                return $return;
            }
        }
        $answerRows = $answers->pluck('row_id')->toArray();
        if(sort($answerRows) !== sort($questionRows)){
            $return['status'] = 422;
            $return['detail'] =  trans('assessment.you must answer all rows questions inside matrix');
            $return['title'] = 'you must answer all rows questions inside matrix';
            return $return;
        }
    }
}
