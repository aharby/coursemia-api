<?php


namespace App\OurEdu\QuestionReport\Repository;


use App\OurEdu\QuestionReport\Models\QuestionReport;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Pagination\LengthAwarePaginator;

interface QuestionReportRepositoryInterface
{

    /**
     * @return LengthAwarePaginator
     */
    public function all(int $subjectId): LengthAwarePaginator;


    /**
     * @param  int  $id
     * @return QuestionReport|null
     */
    public function findOrFail(int $id): ?QuestionReport;

    /**
     * @return LengthAwarePaginator
     */
    public function smeSubjectsHasQuestionsReported(): LengthAwarePaginator;

    public function listReportedSubjectSections($subjectID);

    public function listReportedSectionSections($sectionID);
}
