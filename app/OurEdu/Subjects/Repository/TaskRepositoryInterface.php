<?php

declare(strict_types=1);

namespace App\OurEdu\Subjects\Repository;

use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;

    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param int $id
     * @return Task|null
     */
    public function findOrFail(int $id):?Task;

    public function getAllSMETasksPaginated($sme,  $filters = []);

    public function getAllContentAuthorActiveTasksPaginated($user, $filters = []);

    public function makeTasksAssignedIn(array $tasksIds);
    public function makeTasksUnAssigned(array $tasksIds);

    public function update(array $data);

    public function unAssignTasksFromContentAuthor($contentAuthorID ,array $taskIds);
    public function contentAuthorTask();
    public function contentAuthorTaskDetails($contentAuthor);
}
