<?php


namespace App\OurEdu\QuestionReport\Repository;


use App\OurEdu\QuestionReport\Models\QuestionReport;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionReportRepository implements QuestionReportRepositoryInterface
{
    private $questionReport;

    public function __construct(QuestionReport $questionReport)
    {
        $this->questionReport = $questionReport;
    }

    /**
     * @param  int  $subjectId
     * @return LengthAwarePaginator
     */
    public function all(int $subjectId): LengthAwarePaginator
    {
        return $this->questionReport->notReported()->notIgnored()->where('subject_id', $subjectId)->jsonPaginate(env('PAGE_LIMIT', 20));
    }


    /**
     * @param  int  $id
     * @return QuestionReport|null
     */
    public function findOrFail(int $id): ?QuestionReport
    {
        return $this->questionReport->findOrFail($id);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function smeSubjectsHasQuestionsReported(): LengthAwarePaginator
    {
        return Subject::whereHas('questionReport' , function ($query){
            $query->notReported()->notIgnored();
        })->smeSubject()->paginate(env('PAGE_LIMIT', 20));
    }


    public function ignore() {

        $this->questionReport->is_ignored = 1;
        $this->questionReport->save();

        return true;
    }

    public function report() {

        $this->questionReport->is_reported = 1;
        $this->questionReport->save();

        return true;
    }

    public function listReportedSubjectSections($subjectID) {
        return  Subject::smeSubject()->with('questionReport')
            ->with('questionReportSubjectFormatSubject')
            ->find($subjectID);
    }


    public function listReportedSectionSections($sectionID) {
        return SubjectFormatSubject::with('questionReport')
            ->with('questionReportSubjectFormatSubject')
            ->find($sectionID);
    }
}
