<?php

namespace App\OurEdu\QuestionReport\Repository;

use App\OurEdu\QuestionReport\Models\QuestionReportSubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;

interface QuestionReportSubjectFormatSubjectRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;

    /**
     * @param array $data
     * @return QuestionReportSubjectFormatSubject|null
     */
    public function create(array $data): ?QuestionReportSubjectFormatSubject;


    public function firstOrCreate(array $data): ?QuestionReportSubjectFormatSubject;

    /**
     * @param int $id
     * @return QuestionReportSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?QuestionReportSubjectFormatSubject;

    /**
     * @param QuestionReportSubjectFormatSubject $report
     * @param array $data
     * @return bool
     */
    public function update(QuestionReportSubjectFormatSubject $report, array $data): bool;

    /**
     * @param QuestionReportSubjectFormatSubject $report
     * @return bool
     */
    public function delete(QuestionReportSubjectFormatSubject $report): bool;

}
