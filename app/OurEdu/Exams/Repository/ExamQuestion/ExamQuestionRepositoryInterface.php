<?php

namespace App\OurEdu\Exams\Repository\ExamQuestion;

use Illuminate\Support\Collection;
use App\OurEdu\Exams\Models\ExamQuestion;

interface ExamQuestionRepositoryInterface
{

    /**
     * @param int $id
     * @return ExamQuestion|null
     */
    public function findOrFail(int $id): ?ExamQuestion;

    /**
     * @param ExamQuestion $examQuestion
     * @param array $data
     * @return bool
     */
    public function update(ExamQuestion $examQuestion, array $data): bool;

    /**
     * Get user taken question in old exams
     * @param int $studentId
     * @param int $subjectId
     * @param array $subjectFormats
     * @return Collection|null
     */
    public function getStudentTakenQuestions(int $studentId, int $subjectId, array $subjectFormats): ?Collection;


    public function cloneQuestion(array $options): ExamQuestion;
}
