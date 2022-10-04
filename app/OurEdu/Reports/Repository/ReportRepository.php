<?php


namespace App\OurEdu\Reports\Repository;


use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Reports\Report;
use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportRepository implements ReportRepositoryInterface
{
    private $report;
    use Filterable;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * @return LengthAwarePaginator
     */

    public function all(array $filters = []): LengthAwarePaginator
    {
        $model = $this->applyFilters($this->report, $filters);
        return $model->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param array $data
     * @return Report|null
     */
    public function create(array $data): ?Report
    {
        return Report::create($data);
    }

    /**
     * @param int $id
     * @return Report|null
     */
    public function findOrFail(int $id): ?Report
    {
        return Report::findOrFail($id);
    }

    /**
     * @param Report $report
     * @param array $data
     * @return bool
     */
    public function update(Report $report, array $data): bool
    {
        return $report->update($data);
    }

    /**
     * @param Report $report
     * @return bool
     * @throws \Exception
     */
    public function delete(Report $report): bool
    {
        return $report->delete();
    }

    public function paginateAllReportsForSME($smeSubjectsIds): LengthAwarePaginator
    {
//        $sectionsIds = $this->report
//                    ->whereHasMorph('reportable', [SubjectFormatSubject::class], function ($q) use ($smeSubjectsIds){
//                        $q->whereIn('subject_id', $smeSubjectsIds);
//                    })
//                    ->pluck('id')
//                    ->toArray();
//
//        $resourcesIds = $this->report
//                        ->whereHasMorph('reportable', [ResourceSubjectFormatSubject::class], function ($q) use ($smeSubjectsIds){
//                            $q->whereHas('subjectFormatSubject', function ($q) use ($smeSubjectsIds) {
//                                $q->whereHas('subject', function ($q) use ($smeSubjectsIds) {
//                                    $q->whereIn('id', $smeSubjectsIds);
//                                });
//                            });
//                        })
//                        ->pluck('id')
//                        ->toArray();
//        dd($resourcesIds);
        return $this->report
            ->where('reportable_type', ReportEnum::SUBJECT_MODEL)
            ->whereIn('reportable_id', $smeSubjectsIds)
            ->OrWhereHasMorph('reportable', [SubjectFormatSubject::class], function ($q) use ($smeSubjectsIds){
                $q->whereIn('subject_id', $smeSubjectsIds);
            })
            ->OrWhereHasMorph('reportable', [ResourceSubjectFormatSubject::class], function ($q) use ($smeSubjectsIds){
                $q->whereHas('subjectFormatSubject', function ($q) use ($smeSubjectsIds) {
                    $q->whereIn('subject_id', $smeSubjectsIds);
                });
            })
            ->jsonPaginate();
//        return $this->report
//                ->where('reportable_type', ReportEnum::SUBJECT_MODEL)
//                ->whereIn('reportable_id', $smeSubjectsIds)
//                ->orWhere(function ($query) use ($smeSubjectsIds) {
//                    $query->where('reportable_type', ReportEnum::SUBJECT_FORMAT_SUBJECT_MODEL)
//                        ->whereHasMorph('reportable', [SubjectFormatSubject::class] ,function ($q) use ($smeSubjectsIds){
//                            $q->whereHas('subject', function ($q) use ($smeSubjectsIds) {
//                                $q->whereIn('id', $smeSubjectsIds)
//                                    ->with(['reportable.subject' => function ($q){
//                                        $q->groupBy('id');
//                                    }]);
//                            });
//                        });
//                })
//                ->orWhere(function ($query) use ($smeSubjectsIds) {
//                    $query->where('reportable_type', ReportEnum::RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL)
//                        ->whereHasMorph('reportable', [ResourceSubjectFormatSubject::class] ,function ($q) use ($smeSubjectsIds){
//                            $q->whereHas('subjectFormatSubject', function ($q) use ($smeSubjectsIds) {
//                                $q->whereHas('subject', function ($q) use ($smeSubjectsIds) {
//                                    $q->whereIn('id', $smeSubjectsIds)
//                                       ->with(['reportable.subjectFormatSubject.subject' => function ($q){
//                                           $q->groupBy('id');
//                                       }]);
//                                });
//                            });
//                        });
//                })
////            ->with(['reportable.subjectFormatSubject.subject' => function ($q){
////                $q->groupBy('id');
////            }])
//            ->get();
////                ->jsonPaginate();
    }

    public function paginateSubjectReportsForSME($subjectId): LengthAwarePaginator
    {
        return $this->report
            ->where('reportable_type', ReportEnum::SUBJECT_MODEL)
            ->where('reportable_id', $subjectId)
            ->jsonPaginate();
    }

    public function paginateSubjectSectionsReportsForSME($subjectId): LengthAwarePaginator
    {
       return $this->report
           ->whereHasMorph('reportable', [SubjectFormatSubject::class], function ($q) use ($subjectId){
               $q->where('subject_id', $subjectId);
                // ->where('parent_subject_format_id', null);
           })
           ->jsonPaginate();
    }

    public function paginateSectionReportsForSME($subjectFormatSubjectId): LengthAwarePaginator
    {
        return $this->report
            ->where('reportable_type', ReportEnum::SUBJECT_FORMAT_SUBJECT_MODEL)
            ->where('reportable_id', $subjectFormatSubjectId)
            ->jsonPaginate();
    }

    public function paginateSectionResourcesReportsForSME($subjectFormatSubjectId): LengthAwarePaginator
    {
        return $this->report
            ->whereHasMorph('reportable', [ResourceSubjectFormatSubject::class], function ($q) use ($subjectFormatSubjectId){
                $q->where('subject_format_subject_id', $subjectFormatSubjectId);
            })
            ->jsonPaginate();
    }

    public function paginateResourcesReportsForSME($resourceId): LengthAwarePaginator
    {
        return $this->report
            ->where('reportable_type', ReportEnum::RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL)
            ->where('reportable_id', $resourceId)
            ->jsonPaginate();
    }

    public function listReportedSubjects() {
        return Subject::whereHas('reports')
            ->orWhereHas('reportedSubjectFormatSubject')
            ->jsonPaginate();
    }

    public function listReportedSubjectSections($subjectID) {
       return  Subject::with('reportedSubjectFormatSubject')->find($subjectID);
    }
    public function listReportedSectionSections($sectionID) {
        return SubjectFormatSubject::where('parent_subject_format_id' , $sectionID)
            ->whereHas('reports')
            ->orWhereHas('reportedSubjectFormatSubject')
            ->jsonPaginate();
    }

}
