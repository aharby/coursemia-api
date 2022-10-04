<?php

namespace App\OurEdu\GeneralExams\Repository\GeneralExam;

use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GeneralExamRepository implements GeneralExamRepositoryInterface
{
    private $generalExam;

    public function __construct(GeneralExam $generalExam)
    {
        $this->generalExam = $generalExam;
    }

    public function create($data)
    {
        return $this->generalExam->create($data);
    }

    public function findOrFail($examId)
    {
        return $this->generalExam->findOrFail($examId);
    }

    public function markAsPublished($exam)
    {
        $exam->published_at = now();
        $exam->save();
    }

    public function paginateSmeExams($sme)
    {
        $subjects = $sme->managedSubjects()->pluck('id')->toArray();

        return $this->generalExam->latest()->whereIn('subject_id', $subjects)->jsonPaginate();
    }

    public function update($exam, $data)
    {
        return $exam->update($data);
    }

    public function delete($exam)
    {
        return $exam->delete();
    }

    public function listStudentAvailableExams($subjectId)
    {
        return $this->generalExam->whereNotNull('published_at')
            ->where('subject_id', $subjectId)
            ->jsonPaginate();
    }

    public function returnQuestion($page): ?LengthAwarePaginator
    {
        $perPage = PreparedGeneralExamQuestion::$questionsPerPage;

        $routeName = 'general_exams';

        $questions = $this->generalExam->questions()
            ->with('options')
            ->paginate($perPage, ['*'], 'page', $page);

        return $questions = $questions->withPath(buildScopeRoute(
            "api.student.{$routeName}.get.next-back-questions",
            [
                'examId' => $this->generalExam->id,
                'current_question' => $questions->first()->id ?? null
            ]
        ));
    }

    public function generalExamStudents()
    {
        return $this->generalExam->students()
                    ->where('is_finished', 1)
                    ->withPivot('result')
                    ->with('user')->get();
    }
}
