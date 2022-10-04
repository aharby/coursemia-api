<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;

use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AssessmentRepositoryInterface
{
    /**
     * @param $data
     * @return Assessment
     */
    public function create($data): Assessment;


    /**
     * @param $data
     * @return bool
     */
    public function update($data): bool;


    /**
     * @return bool
     */
    public function delete(): bool;


    /**
     * @param $assessmentId
     * @return Assessment|null
     */
    public function findOrFail($assessmentId): ?Assessment;


    /**
     * @param $assessmentId
     * @param $filters
     * @return Assessment|null
     */
    public function findOrFailByMultiFields($assessmentId, $filters): ?Assessment;


    public function setAssessment(Assessment $assessment);

    public function getAssessment(): Assessment;

    public function saveAssessmentAssessors(Assessment $assessment, $assessorsIds);

    public function saveAssessmentAssessees(Assessment $assessment, $assesseesIds);

    public function saveAssessmentViewers(Assessment $assessment, $assesseesIds);

    public function listAssessmentManagerAssessments();

    public function getAssessmentQuestions(Assessment $assessment);

    public function returnQuestion(int $page, int $perPage = null): ?LengthAwarePaginator;

    public function listAssessmentManagerAssessmentsReport();

    public function listAssessmentReportForResultViewers(bool $isPaginate = true);

    /**
     * @param array $filters
     * @param bool $isPaginate
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public function assessmentsWithFilter(array $filters = [], bool $isPaginate = true);

    public function listSchoolAdminAssessmentsReport($isPaginate = true, $filters = []);

    /**
     * @param Assessment $assessment
     * @param array $filter
     * @param bool $isPaginate
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public function getAssessmentQuestionsWithFilter(
        Assessment $assessment,
        array $filter = [],
        bool $isPaginate = true
    ): LengthAwarePaginator|array|Collection;

    public function getAssessmentWithQuestion($isPaginate = true, $filters = []);

    public function questionAnswersPercentage(int $assessmentId);
}
