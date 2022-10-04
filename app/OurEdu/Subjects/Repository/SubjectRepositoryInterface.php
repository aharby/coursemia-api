<?php

declare(strict_types=1);

namespace App\OurEdu\Subjects\Repository;

use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SubjectRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param array $withCount
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginateWithCount(array $withCount, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param array $where
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param int $id
     * @return Subject|null
     */
    public function findOrFail(int $id): ?Subject;

    /**
     * @param array $data
     * @return Subject
     */
    public function create(array $data): Subject;

    /**
     * @param array $data
     * @return Subject|null
     */
    public function update(array $data): ?Subject;

    /**
     * @param Subject $subject
     * @return bool
     */
    public function delete(): bool;

    public function getContentAuthorsIds(): array;

    public function getInstructorsIds(): array;

    public function attachContentAuthors(array $ids);

    public function syncContentAuthors(array $ids);

    public function attachInstructors(array $ids);

    public function syncInstructors(array $ids);

    public function deleteAllSubjectFormatSubjects();

    public function generateTask(int $subjectFormatSubjectId, int $resourceSubjectFormatSubjectId, array $data): ?Task;

    public function getSubjectTasks();

    public function getSubjectTasksForSMEPaginated(array $filters = []);

    public function getSubjectActiveTasksForContentAuthorPaginated($contentAuthorId, array $filters = []);

    public function attachMedia($id, $data);

    public function pluck();

    public function getLikedSubjectFormatSubjectByUser(int $userId, Subject $subject, int $subjectFormatSubjectId);

    public function setPracticesNumber($practices);

    public function getPluckSubjectsToArray(): array;

    public function paginateFilteredSubjects();

    public function pluckSubjectsFilteredToArray(
        $countryId,
        $educationalSystemId,
        $gradeClassId,
        $academicalYearId
    );


    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function getSubjectWithSuccessRateAndExamCount(array $filter = []): LengthAwarePaginator;


    /**
     * @param array $filter
     * @return Collection|null
     */
    public function getExportSubjectWithSuccessRateAndExamCount(array $filter = []): ?\Illuminate\Database\Eloquent\Collection;
    /**
     * @return bool
     */
    public function makeSubjectsOutOfDate(): bool;

    public function getAllStudentsProgress($subjectId);

    public function getGradeBranchSubjectsPluck(SchoolAccountBranch $branch, GradeClass $gradeID);

    public function dataExport($orders = []): ?\Illuminate\Database\Eloquent\Collection;

    public function getSubjectWithFinishedVCRSessionsCount();

    public function updateUsingModel($subjectId, $data);

    public function paginateWhereQudratStudent(
        array $studentData,
        $perPage = null,
        $pageName = 'page',
        $page = null
    ): LengthAwarePaginator;

    public function firstOrFailWithUuid(string $uuid): ?Subject;
}
