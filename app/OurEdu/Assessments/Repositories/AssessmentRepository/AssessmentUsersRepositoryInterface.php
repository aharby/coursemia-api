<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\OurEdu\Users\User;
use App\OurEdu\Assessments\Models\AssessmentAssessor;
use App\OurEdu\Assessments\Models\AssessmentAssessee;
use App\OurEdu\Assessments\Models\AssessmentUser;

interface AssessmentUsersRepositoryInterface
{
    /**
     * @param User $assessor
     * @return LengthAwarePaginator
     */
    public function getAssessmentsByAssessor(User $assessor): LengthAwarePaginator;

    /**
     * @param User $assessee
     * @return LengthAwarePaginator
     */
    public function getAssessmentsByAssessee(User $assessee): LengthAwarePaginator;

    public function findAssessorAssessment(int $assessmentId,int $assessorId):?AssessmentAssessor;

    public function findAssesseeAssessment(int $assessmentId,int $assesseeId):?AssessmentAssessee;

    public function startAssessment(array $data);

    public function getUserAssessment($assessmentId,$assesseeId,$assessorId):?AssessmentUser;

    public function getAssessorAnswersScore($assessmentId , $assessorId);

    public function update($assessmentId , $data);

    public function getAssessorAnswersCount($assessmentId,$assessorId);

    public function getAssessorAssessees(Assessment $assessment, User $assessor);

    public function getAssesseeAssessors($assessmentId, $assesseeId);

    public function getAssesseeByAssessorId(Assessment $assessment ,int $assessorId,bool $isPaginate=true);

    public function getAssessedUsersOfAssessor(Assessment $assessment, int $assessorId, User $user);

    public function getAssessmentAssessors(Assessment $assessment,bool $isPaginate=true,User $user = null);

    public function viewerAssessments(User $user, bool $isPaginate=true);

    public function getAssesseeDetailsByAssessor(Assessment $assessment, User $assessor, User $assessee, bool $isPaginate=true);

    public function getAssesseeByViewerId(Assessment $assessment ,User $user);

    public function getAllUserFinishedAssessment($assessmentId,$assesseeId);

    public function getAllAssesseeByViewerId(Assessment $assessment ,User $viewer);

    public function getAllViewersByAssesse(Assessment $assessment ,User $asssess);

    public function getUserCountedAssessment($assessmentId,$assesseeId,$assessorId);

    public function getGroupedAssessAssessors($assessmentId, $assesseeId);

    public function getAssessmentIdsOfAssessorGeneralType(User $user, SchoolAccount $school);

    public function getAssessmentIdsOfResultViewerGeneralType(User $user, SchoolAccount $school);

    public function getAssessmentIdsOfAssesseeGeneralType(User $user, SchoolAccount $school);
}
