<?php


namespace App\OurEdu\Assessments\Jobs;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddUserToAssessmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * addUserToAssessmentJob constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(AssessmentUsersRepositoryInterface $assessmentUsersRepository)
    {
        if ($school = $this->getUserSchool() and in_array($this->user->type, UserEnums::assessmentUsers())) {
            
            $assessorAssessmentIds = $assessmentUsersRepository->getAssessmentIdsOfAssessorGeneralType($this->user,$school);
            $this->user->assessmentsAsAssessor()->syncWithoutDetaching($assessorAssessmentIds);

            $assesseeAssessmentIds = $assessmentUsersRepository->getAssessmentIdsOfAssesseeGeneralType($this->user,$school);
            $this->user->assessmentsAsAssessee()->syncWithoutDetaching($assesseeAssessmentIds);

            $resultViewersAssessmentIds = $assessmentUsersRepository->getAssessmentIdsOfResultViewerGeneralType($this->user,$school);
            $this->user->assessmentsAsViewer()->syncWithoutDetaching($resultViewersAssessmentIds);
        }
    }

    private function getUserSchool()
    {

        $school =  match ($this->user->type) {
            UserEnums::SCHOOL_ACCOUNT_MANAGER => $this->user->schoolAccount,
            UserEnums::SCHOOL_LEADER => $this->user->schoolLeader->schoolAccount,
            UserEnums::SCHOOL_SUPERVISOR => $this->user->schoolSupervisor->schoolAccount,
            UserEnums::EDUCATIONAL_SUPERVISOR => $this->user->branches->first()->schoolAccount,
            UserEnums::SCHOOL_INSTRUCTOR=> $this->user->branch->schoolAccount,
            UserEnums::ACADEMIC_COORDINATOR => $this->user->branch->schoolAccount,
            default => false
        };

         return $school;

    }
}
