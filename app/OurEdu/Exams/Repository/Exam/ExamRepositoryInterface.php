<?php

namespace App\OurEdu\Exams\Repository\Exam;


use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ExamRepositoryInterface
{
    /**
     * @param array $data
     * @return PrepareExamQuestion|null
     */
    public function create(array $data): ?Exam;


    /**
     * @param int $id
     * @return PrepareExamQuestion|null
     */
    public function findOrFail(int $id): ?Exam;

    /**
     * @param Exam $prepareExamQuestion
     * @param array $data
     * @return Exam|null
     */
    public function update(Exam $prepareExamQuestion, array $data): ?Exam;

    public function returnQuestion( $page): ?LengthAwarePaginator;

    public function findOrFailExamQuestion($questionId);

    public function listPreviousExams($studentId , array $filters = []): LengthAwarePaginator;

    public function listPractices($studentId , array $filters = []): LengthAwarePaginator;

    public function practicesWithSubjectIds();

    public function getStudentsSpeedPercentageOrderInSubject($subjectId):array ;

    public function cloneExam(array $options): Exam;

    public function createChallenge(array $data);

    public function updateStudentsRankInCompetition(Exam $competition);

}
