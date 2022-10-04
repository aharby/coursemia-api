<?php

namespace App\OurEdu\Assessments\Assessor\UseCases\NextAndBack;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepository;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
/**
 * Next and previous question  use case
 */
class AssessmentNextBackUseCase implements AssessmentNextBackUseCaseInterface
{
    protected $user;
    protected $assessmentRepo;
    protected $assessmentUsersRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepo, AssessmentUsersRepositoryInterface $assessmentUsersRepository)
    {
        $this->user = Auth::guard('api')->user();
        $this->assessmentRepo = $assessmentRepo;
        $this->assessmentUsersRepository = $assessmentUsersRepository;
    }

    public function nextOrBackQuestion(int $assessmentId, int $assesseeId, int $page)
    {
        $assessorId = auth()->user()->id;

        $assessment = $this->assessmentRepo->findOrFail($assessmentId);

        $userAssessment = $this->assessmentUsersRepository->getUserAssessment($assessmentId, $assesseeId, $assessorId);

        return $this->getQuestions($assessment, $userAssessment, $page);
    }

    public function getQuestions(Assessment $assessment, AssessmentUser $userAssessment, int $page, int $perPage = null)
    {
        $assessmentRepo = new AssessmentRepository($assessment);
        $assessmentQuestions = $assessmentRepo->returnQuestion($page, $perPage);

        $validationError = $this->validateNextOrBackQuestion($assessmentQuestions, $userAssessment, $assessment);

        if ($validationError) {
            return $validationError;
        }

        if ($assessmentQuestions->currentPage() == $assessmentQuestions->lastPage()) {
            $return['last_question'] = true;
        }

        $return['status'] = 200;
        $return['assessment'] = $assessment;
        $return['questions'] = $assessmentQuestions;

        return $return;
    }

    private function validateNextOrBackQuestion($assessmentQuestions,$userAssessment,$assessment){
        if ($assessmentQuestions->currentPage() > $assessmentQuestions->lastPage()) {
            $return['status'] = 422;
            $return['detail'] = trans('exam.This question not found');
            $return['title'] = 'This question not found';
            return $return;
        }

        if (! $userAssessment) {
            $return['status'] = 422;
            $return['detail'] = trans('assessment.assessment not started yet');
            $return['title'] = 'error not started yet';
            return $return;
        }
    }
}
