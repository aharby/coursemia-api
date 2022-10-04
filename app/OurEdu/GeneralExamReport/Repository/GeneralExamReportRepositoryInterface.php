<?php

namespace App\OurEdu\GeneralExamReport\Repository;


use App\OurEdu\GeneralExamReport\Models\GeneralExamReport;
use App\OurEdu\GeneralExamReport\Models\GeneralExamReportQuestion;

interface GeneralExamReportRepositoryInterface
{
    public function findByGeneralExamIdOrFail($examId) : ?GeneralExamReport;

    public function findQuestionOrFail($questionId) : ?GeneralExamReportQuestion;

    public function smeSubjectsHasGeneralExamsWithReports();

    public function listSubjectReportedQuestions($subjectId);

    public function listGeneralExamReportedQuestions($generalExamId);

    public function generalExamReportQuestionDetails($questionId);

    public function ignoreQuestion($reportQuestionId);

    public function reportQuestion($reportQuestionId);

    public function listReportedSubjectSections($subjectID);

    public function listReportedSectionSections($sectionID);
}
