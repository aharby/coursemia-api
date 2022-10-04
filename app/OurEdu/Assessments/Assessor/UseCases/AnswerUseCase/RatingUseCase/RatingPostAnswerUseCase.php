<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\RatingUseCase;

use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingOptions;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class RatingPostAnswerUseCase implements RatingPostAnswerUseCaseInterface
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

        $error = $this->validate($answers, $question);
        if ($error) {
            return $error;
        }
        if ($answer = $answers->first()) {
            $option = $question->options->where('id', $answer->answer_id)->first();
            $score = $this->calculateScore($question, $option);
            $answersData[] = [
                'option_id' => $answer->answer_id,
                'option_type'=> AssissmentRatingOptions::class,
                'assessment_question_id' => $assessmentQuestion->id,
                'user_id'    =>  $this->user->id,
                'score'=>$option->grade
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

    protected function validate($answers, $question){
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

            if($question->assessmentQuestion->slug === QuestionTypesEnums::STAR_RATING) {
            $return['status'] = 422;
            $return['detail'] =  trans('assessment.you_should_select_one_star_at_least');
            $return['title'] = 'you_should_select_one_star_at_least';
            }

            return $return;
        }

        $questionOption = $question->options
            ->where('id', $answer->answer_id)
            ->first();


        if(!$questionOption){
            $return['status'] = 422;
            $return['detail'] =  trans('assessment.you have to answer the answer');
            $return['title'] = 'Invalid answer';
            return $return;
        }
    }

    /**
     * @param AssissmentRatingQuestion $question
     * @param AssissmentRatingOptions $answerOption
     * @return float
     */
    protected function calculateScore(AssissmentRatingQuestion $question, AssissmentRatingOptions $answerOption): float
    {
        return $answerOption->grade;
    }
}
