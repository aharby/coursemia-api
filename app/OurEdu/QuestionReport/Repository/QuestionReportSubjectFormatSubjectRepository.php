<?php


namespace App\OurEdu\QuestionReport\Repository;


use App\OurEdu\QuestionReport\Models\QuestionReportSubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionReportSubjectFormatSubjectRepository implements QuestionReportSubjectFormatSubjectRepositoryInterface
{
    private $report;

    public function __construct(QuestionReportSubjectFormatSubject $report)
    {
        $this->report = $report;
    }

    /**
     * @return LengthAwarePaginator
     */

    public function all(): LengthAwarePaginator
    {
        return $this->report->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param array $data
     * @return QuestionReportSubjectFormatSubject|null
     */
    public function create(array $data): ?QuestionReportSubjectFormatSubject
    {
        return QuestionReportSubjectFormatSubject::create($data);
    }

    public function firstOrCreate(array $data): ?QuestionReportSubjectFormatSubject
    {
        return QuestionReportSubjectFormatSubject::firstOrCreate($data);
    }

    /**
     * @param int $id
     * @return QuestionReportSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?QuestionReportSubjectFormatSubject
    {
        return QuestionReportSubjectFormatSubject::findOrFail($id);
    }

    /**
     * @param QuestionReportSubjectFormatSubject $report
     * @param array $data
     * @return bool
     */
    public function update(QuestionReportSubjectFormatSubject $report, array $data): bool
    {
        return $report->update($data);
    }

    /**
     * @param QuestionReportSubjectFormatSubject $report
     * @return bool
     * @throws \Exception
     */
    public function delete(QuestionReportSubjectFormatSubject $report): bool
    {
        return $report->delete();
    }

}
