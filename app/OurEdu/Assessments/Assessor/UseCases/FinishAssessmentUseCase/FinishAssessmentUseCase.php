<?php

namespace App\OurEdu\Assessments\Assessor\UseCases\FinishAssessmentUseCase;

use App\OurEdu\Assessments\Jobs\CreateViewersAvgScoreJob;
use App\OurEdu\Assessments\Jobs\UpdateAssesseCountJob;
use App\OurEdu\Assessments\Jobs\UpdateAssessmentBranchesScoresJob;
use App\OurEdu\Assessments\Jobs\UpdateAssessmentUsersScoreJob;
use App\OurEdu\Assessments\Jobs\UpdateQuestionsBranchesScoresJob;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepository;
use App\OurEdu\Assessments\Jobs\UpdateAssessAvgScoreAfterFinishJob;

class FinishAssessmentUseCase implements FinishAssessmentUseCaseInterface
{
    private $assessmentRepo;
    private $assessmentUsersRepo;

    public function __construct(
        AssessmentRepositoryInterface $assessmentRepo,
        AssessmentUsersRepositoryInterface $assessmentUsersRepo
    ) {
        $this->assessmentRepo = $assessmentRepo;
        $this->assessmentUsersRepo = $assessmentUsersRepo;
    }


    // 4- validate that student can finish the homework.
    public function finishAssessment(int $assessmentId, int $assessorId,int $assesseeId)
    {
        $assessment = $this->assessmentRepo->findOrFail($assessmentId);

        $userAssessment = $this->assessmentUsersRepo->getUserAssessment($assessmentId,$assesseeId,$assessorId);

        $error = $this->validateFinishAssessment($assessment,$userAssessment);
        if($error){
            return $error;
        }

        $score=$this->assessmentUsersRepo->getAssessorAnswersScore($assessment->id , $assessorId);
        $data = [
            'is_finished' => 1,
            'end_at' => now()
        ];

        if ($userAssessment->is_finished != 1) {
            $this->assessmentUsersRepo->update($userAssessment->id, $data);
            $assessorAssessment = $this->assessmentUsersRepo->findAssessorAssessment($assessment->id, $assessorId);

            UpdateAssessmentUsersScoreJob::dispatch($assessment, $userAssessment, $assessorAssessment);
            UpdateAssesseCountJob::dispatch($assessment, $assesseeId, $userAssessment, $assessorId);
            UpdateQuestionsBranchesScoresJob::dispatch($assessment);
            UpdateAssessmentBranchesScoresJob::dispatch($assessment);
            CreateViewersAvgScoreJob::dispatch($assessment);

            $return['status'] = 200;
            $return['message'] = trans('assessment.you finished assessment successfully');
            return $return;
        }
    }

    private function validateFinishAssessment($assessment,$userAssessment)
    {
        if(!$userAssessment){
            $return['status'] = 422;
            $return['detail'] = trans('assessment.Assessment Didn\'t Started yet');
            $return['title'] = 'assessment_didnt_start_yet';
            return $return;
        }

        if ($userAssessment->is_finished == 1) {
            $return['status'] = 422;
            $return['detail'] = trans('assessment.The assessment is already finished');
            $return['title'] = 'assessment_already_finished';
            return $return;
        }
    }
}
