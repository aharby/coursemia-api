<?php

namespace App\OurEdu\Reports\Repository;

use App\OurEdu\Reports\ReportSubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReportSubjectFormatSubjectRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;

    /**
     * @param array $data
     * @return ReportSubjectFormatSubject|null
     */
    public function create(array $data): ?ReportSubjectFormatSubject;


    public function firstOrCreate(array $data): ?ReportSubjectFormatSubject;

    /**
     * @param int $id
     * @return ReportSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?ReportSubjectFormatSubject;

    /**
     * @param ReportSubjectFormatSubject $report
     * @param array $data
     * @return bool
     */
    public function update(ReportSubjectFormatSubject $report, array $data): bool;

    /**
     * @param ReportSubjectFormatSubject $report
     * @return bool
     */
    public function delete(ReportSubjectFormatSubject $report): bool;

}
