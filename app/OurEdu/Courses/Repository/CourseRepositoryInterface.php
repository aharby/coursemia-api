<?php

declare(strict_types=1);

namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\Task;
use App\OurEdu\Users\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseRepositoryInterface
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
     * @return Course|null
     */
    public function findOrFail(int $id): ?Course;

    /**
     * @param array $data
     * @return Course
     */
    public function create(array $data): Course;

    /**
     * @param array $data
     * @return Course|null
     */
    public function update(array $data): ?Course;

    /**
     * @param Course $subject
     * @return bool
     */
    public function delete(): bool;


    /**
     * @return bool
     */
    public function makeCoursesOutOfDate(): bool;

    /**
     * @param User $instructor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCoursesByInstructor(User $instructor);
}
