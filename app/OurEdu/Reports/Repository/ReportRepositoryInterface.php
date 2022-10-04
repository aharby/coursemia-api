<?php

namespace App\OurEdu\Reports\Repository;

use App\OurEdu\Reports\Report;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReportRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;

    /**
     * @param array $data
     * @return Report|null
     */
    public function create(array $data): ?Report;

    /**
     * @param int $id
     * @return Report|null
     */
    public function findOrFail(int $id): ?Report;

    /**
     * @param Report $report
     * @param array $data
     * @return bool
     */
    public function update(Report $report, array $data): bool;

    /**
     * @param Report $report
     * @return bool
     */
    public function delete(Report $report): bool;

    /**
     * @param $smeSubjectsIds
     * @return LengthAwarePaginator
     */
    public function paginateAllReportsForSME($smeSubjectsIds): LengthAwarePaginator;

    /**
     * @param $subjectId
     * @return LengthAwarePaginator
     */
    public function paginateSubjectReportsForSME($subjectId): LengthAwarePaginator;

    /**
     * @param $subjectId
     * @return LengthAwarePaginator
     */
    public function paginateSubjectSectionsReportsForSME($subjectId): LengthAwarePaginator;

    /**
     * @param $resourceId
     * @return LengthAwarePaginator
     */
    public function paginateResourcesReportsForSME($resourceId): LengthAwarePaginator;

    /**
     * @param $subjectFormatSubjectId
     * @return LengthAwarePaginator
     */
    public function paginateSectionReportsForSME($subjectFormatSubjectId): LengthAwarePaginator;

    /**
     * @param $subjectFormatSubjectId
     * @return LengthAwarePaginator
     */
    public function paginateSectionResourcesReportsForSME($subjectFormatSubjectId): LengthAwarePaginator;

    public function listReportedSubjects();

    public function listReportedSubjectSections($subjectID);

    public function listReportedSectionSections($sectionID);

}
