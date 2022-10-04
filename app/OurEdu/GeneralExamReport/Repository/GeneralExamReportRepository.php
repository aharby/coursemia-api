<?php

namespace App\OurEdu\GeneralExamReport\Repository;

use App\OurEdu\GeneralExamReport\Models\GeneralExamReport;
use App\OurEdu\GeneralExamReport\Models\GeneralExamReportQuestion;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;
use function foo\func;

class GeneralExamReportRepository implements GeneralExamReportRepositoryInterface
{
    private $generalExamReport;

    public function __construct(GeneralExamReport $generalExam)
    {
        $this->generalExamReport = $generalExam;
    }


    public function findByGeneralExamIdOrFail($examId) : ?GeneralExamReport
    {
        return $this->generalExamReport->where('general_exam_id' , $examId)->firstOrFail();
    }

    public function findQuestionOrFail($questionId) : ?GeneralExamReportQuestion
    {
        return GeneralExamReportQuestion::findOrFail($questionId);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function smeSubjectsHasGeneralExamsWithReports(): LengthAwarePaginator
    {
        return Subject::whereHas('generalExams' , function ($query){
            $query->whereHas('report' , function ($query) {
                $query->whereHas('reportQuestion' , function ($query){
                        $query->where('preference_parameter' , '<=' ,0)->notReported()->notIgnored();
                });
            });
        })->smeSubject()->paginate(env('PAGE_LIMIT', 20));
    }


    public function listSubjectReportedQuestions($subjectId){

        return GeneralExamReportQuestion::where('preference_parameter' , '<=' ,0)->notReported()->notIgnored()
            ->whereHas('report' , function($query) use ($subjectId){
                $query->whereHas('generalExam', function ($query) use ($subjectId){
                    $query->where('subject_id' , $subjectId);
                });
            })->jsonPaginate();

    }
    public function listGeneralExamReportedQuestions($generalExamId){

        return GeneralExamReportQuestion::where('general_exam_id',$generalExamId)->where('preference_parameter' , '<=' ,0)->notReported()->notIgnored()
         ->jsonPaginate();

    }
    public function generalExamReportQuestionDetails($questionId){

        return GeneralExamReportQuestion::findOrFail($questionId);
    }

    public function ignoreQuestion($reportQuestionId) {
        $question = GeneralExamReportQuestion::findOrFail($reportQuestionId);
        $question->is_ignored = true;
        $question->save();
    }

    public function reportQuestion($reportQuestionId) {
        $question = GeneralExamReportQuestion::findOrFail($reportQuestionId);
        $question->is_reported = true;
        $question->save();
    }


    public function listReportedSubjectSections($subjectID) {
        return  Subject::smeSubject()->with('questionReport')
            ->with('generalExamQuestionReportSubjectFormatSubject')
            ->find($subjectID);
    }


    public function listReportedSectionSections($sectionID) {
        return SubjectFormatSubject::with('questionReport')
            ->with('generalExamQuestionReportSubjectFormatSubject')
            ->find($sectionID);
    }

}
