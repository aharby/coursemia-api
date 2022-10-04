<?php

namespace App\OurEdu\Exams\Repository\PrepareExamQuestion;

use App\OurEdu\Exams\Models\PrepareExamQuestion;

interface PrepareExamQuestionRepositoryInterface
{
    /**
     * @param array $data
     * @return PrepareExamQuestion|null
     */
    public function create(array $data): ?PrepareExamQuestion;


    /**
     * @param int $id
     * @return PrepareExamQuestion|null
     */
    public function findOrFail(int $id): ?PrepareExamQuestion;

    /**
     * @param PrepareExamQuestion $prepareExamQuestion
     * @param array $data
     * @return PrepareExamQuestion|null
     */
    public function update(PrepareExamQuestion $prepareExamQuestion, array $data): ?PrepareExamQuestion;


    public function getCountBySubjectFormat($sectionIds, $difficultyLevel);

    public function getBySubjectFormats(array $subjectFormatIds);

    public function getStudentNotTakenQuestions(
        int $studentId,
        int $subjectFormatId,
        int $limit,
        array $levels,
        string $generationType
    );
}
