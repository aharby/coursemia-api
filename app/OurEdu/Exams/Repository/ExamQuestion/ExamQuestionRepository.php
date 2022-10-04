<?php

namespace App\OurEdu\Exams\Repository\ExamQuestion;

use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Collection;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;

class ExamQuestionRepository implements ExamQuestionRepositoryInterface
{
    private ExamQuestion $examQuestion;

    public function __construct(ExamQuestion $examQuestion)
    {
        $this->examQuestion = $examQuestion;
    }

    /**
     * @param int $id
     * @return ExamQuestion|null
     */
    public function findOrFail(int $id): ?ExamQuestion
    {
        return ExamQuestion::findOrFail($id);
    }

    /**
     * @param ExamQuestion $examQuestion
     * @param array $data
     * @return bool
     */
    public function update(ExamQuestion $examQuestion, array $data): bool
    {
        return $examQuestion->update($data);
    }

    /**
     * @return TrueFalseQuestion|null
     */
    public function trueFalseQuestion(): ?TrueFalseQuestion
    {
        return $this->examQuestion->questionable()->firstOrFail();
    }

    /**
     * Get user taken question in old exams
     * @param int $studentId
     * @param int $subjectId
     * @param array $subjectFormats
     * @return Collection|null
     */
    public function getStudentTakenQuestions(int $studentId, int $subjectId, array $subjectFormats): ?Collection
    {
        return ExamQuestion::where('subject_id', $subjectId)
            ->whereIn('subject_format_subject_id', $subjectFormats)
            ->whereHas('exam', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->with('questionable')
            ->get();
    }

    public function cloneQuestion(array $options): ExamQuestion
    {
        $examQuestionData = $this->examQuestion->toArray();

        $clonedData = array_merge($examQuestionData, $options);

        return $this->examQuestion->create($clonedData);
    }
}
