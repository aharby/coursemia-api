<?php

namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LiveSessionRepositoryInterface
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
     * @param array $where
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param int $id
     * @return LiveSession|null
     */
    public function findOrFail(int $id): ?LiveSession;

    /**
     * @param array $data
     * @return LiveSession
     */
    public function create(array $data): LiveSession;

    /**
     * @param array $data
     * @return LiveSession|null
     */
    public function update(array $data): ?LiveSession;

    /**
     * @param LiveSession $subject
     * @return bool
     */
    public function delete(): bool;

    /**
     * @param  Student  $student
     * @return LengthAwarePaginator
     */
    public function getRelatedLiveSessionsForStudent(Student $student): LengthAwarePaginator;

    public function getRelatedLiveSessionsForInstructor(Instructor $instructor): LengthAwarePaginator;
}
