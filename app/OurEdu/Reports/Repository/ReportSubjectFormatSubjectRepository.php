<?php


namespace App\OurEdu\Reports\Repository;


use App\OurEdu\Reports\ReportSubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportSubjectFormatSubjectRepository implements ReportSubjectFormatSubjectRepositoryInterface
{
    private $report;

    public function __construct(ReportSubjectFormatSubject $report)
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
     * @return ReportSubjectFormatSubject|null
     */
    public function create(array $data): ?ReportSubjectFormatSubject
    {
        return ReportSubjectFormatSubject::create($data);
    }

    public function firstOrCreate(array $data): ?ReportSubjectFormatSubject
    {
        return ReportSubjectFormatSubject::firstOrCreate($data);
    }

    /**
     * @param int $id
     * @return ReportSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?ReportSubjectFormatSubject
    {
        return ReportSubjectFormatSubject::findOrFail($id);
    }

    /**
     * @param ReportSubjectFormatSubject $report
     * @param array $data
     * @return bool
     */
    public function update(ReportSubjectFormatSubject $report, array $data): bool
    {
        return $report->update($data);
    }

    /**
     * @param ReportSubjectFormatSubject $report
     * @return bool
     * @throws \Exception
     */
    public function delete(ReportSubjectFormatSubject $report): bool
    {
        return $report->delete();
    }

}
