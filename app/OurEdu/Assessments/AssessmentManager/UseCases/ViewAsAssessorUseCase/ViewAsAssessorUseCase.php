<?php


namespace App\OurEdu\Assessments\AssessmentManager\UseCases\ViewAsAssessorUseCase;

use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ViewAsAssessorUseCase implements ViewAsAssessorUseCaseInterface
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

    public function nextOrBackQuestion(int $assessmentId, int $page): array
    {
        $assessment = $this->assessmentRepo->findOrFail($assessmentId);

        $assessmentRepo = new AssessmentRepository($assessment);

        $assessmentQuestions = $assessmentRepo->returnQuestion($page);

        $validationError = $this->validateNextOrBackQuestion($assessmentQuestions);

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


    private function validateNextOrBackQuestion($assessmentQuestions): array
    {
        $return = [];
        if ($assessmentQuestions->currentPage() > $assessmentQuestions->lastPage()) {
            $return['status'] = 422;
            $return['detail'] = trans('exam.This question not found');
            $return['title'] = 'This question not found';
            return $return;
        }
        return $return;
    }
}
