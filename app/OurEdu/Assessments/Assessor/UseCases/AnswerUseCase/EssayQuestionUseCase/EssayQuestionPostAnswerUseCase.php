<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\AnswerUseCase\EssayQuestionUseCase;


use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class EssayQuestionPostAnswerUseCase implements EssayQuestionPostAnswerUseCaseInterface
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function postAnswer(AssessmentRepository $assessmentRepository, AssessmentQuestion $assessmentQuestion, Collection $answers, $questionAnswerData)
    {
        $answerText = trim($answers->first()?->answer_text);

        if(!$answerText) {
            $return['status'] = 422;
            $return['detail'] =  trans('assessment.answer_id_required');
            $return['title'] = 'answer_text is required';

            return $return;
        }

        $questionAnswer =  [
            'user_id'    =>  $this->user->id,
            'assessment_id'    =>  $assessmentRepository->assessment->id,
            'assessment_question_id'    =>  $assessmentQuestion->id,
            'score' => 0,
            'answer_text' => $answerText,
        ];

        return $questionAnswer;
    }
}
