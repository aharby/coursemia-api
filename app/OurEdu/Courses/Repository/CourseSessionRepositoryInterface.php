<?php

declare(strict_types=1);
namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\User;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\CourseSessions\Models\SubModels\Task;
use App\OurEdu\Courses\Models\SubModels\CourseSession;

interface CourseSessionRepositoryInterface
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
     * @return CourseSession|null
     */
    public function findOrFail(int $id): ?CourseSession;

    /**
     * @param array $data
     * @return CourseSession|null
     */
    public function update(array $data): ?CourseSession;

    /**
     * @param CourseSession $subject
     * @return bool
     */
    public function delete(): bool;


    public function getRelatedCourseSessionsForInstructor(User $instructor) : LengthAwarePaginator;

    public function getCourseSessionsForInstructor(User $instructor, Course $course) : LengthAwarePaginator;
}
