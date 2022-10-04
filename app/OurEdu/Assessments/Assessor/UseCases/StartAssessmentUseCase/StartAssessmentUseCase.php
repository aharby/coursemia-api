<?php


namespace App\OurEdu\Assessments\Assessor\UseCases\StartAssessmentUseCase;

use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;


class StartAssessmentUseCase implements StartAssessmentUseCaseInterface
{
    private $assessmentRepo;
    private $assessmentUsersRepository;

    public function __construct(
        AssessmentRepositoryInterface $assessmentRepo,
        AssessmentUsersRepositoryInterface $assessmentUsersRepository
    ) {
        $this->assessmentRepo = $assessmentRepo;
        $this->assessmentUsersRepository = $assessmentUsersRepository;
    }

    public function startAssessment(int $assessmentId,int $assesseeId)
    {
        $assessorId = auth()->user()->id;
        $assessment = $this->assessmentRepo->findOrFail($assessmentId);

        $userAssessment = $this->assessmentUsersRepository->getUserAssessment($assessmentId,$assesseeId,$assessorId);


        $validationErrors = $this->assessmentValidations($assessment,$userAssessment);
        if ($validationErrors) {
            return $validationErrors;
        }

        $assessmentRepo = new AssessmentRepository($assessment);


        if(!$userAssessment){
            $this->assessmentUsersRepository->startAssessment([
                'assessment_id'=>$assessmentId,'start_at'=>now(),
                'user_id'=>$assessorId,'assessee_id'=>$assesseeId
            ]);
        }
        $questions = $assessmentRepo->returnQuestion(1);
        $return['status'] = 200;
        $return['assessment'] = $assessment;
        $return['questions'] = $questions;
        if ($questions->currentPage() == $questions->lastPage()) {
            $return['last_question'] = true;
        }
        $return['message'] = trans('assessment.The assessment started successfully');
        return $return;
    }


    private function assessmentValidations($assessment,$userAssessment)
    {
       // assessment has not published yet
        if (is_null($assessment->published_at)) {
            $return['status'] = 422;
            $return['detail'] = trans('assessment.cant get un published assessment');
            $return['title'] = 'cant get un published assessment';
            return $return;
        }

        // assessment time passed
        if (!is_null($assessment->end_at) && now() > $assessment->end_at) {
            $return['status'] = 422;
            $return['detail'] = trans('assessment.assessment time passed');
            $return['title'] = 'assessment time passed';
            return $return;
        }

        // assessment time has not come yet
        if (!is_null($assessment->start_at) && now() < $assessment->start_at) {
            $return['status'] = 422;
            $return['detail'] = trans('assessment.assessment time has not come yet');
            $return['title'] = 'assessment time has not come yet';
            return $return;
        }

        // student has finished the assessment already
        if ($userAssessment && $userAssessment->is_finished){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.this assessment has been already taken');
            $useCase['title'] = 'this assessment has been already taken';
            return $useCase;
        }
    }

}
