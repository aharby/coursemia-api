<?php

namespace App\OurEdu\GeneralExams\Repository\GeneralExam;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface GeneralExamRepositoryInterface
{
    public function create($data);
    public function findOrFail($examId);
    public function markAsPublished($exam);
    public function listStudentAvailableExams($subjectId);
    public function returnQuestion($page): ?LengthAwarePaginator;
    public function generalExamStudents();
}
