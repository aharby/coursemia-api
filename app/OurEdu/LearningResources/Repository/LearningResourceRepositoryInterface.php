<?php

declare(strict_types=1);

namespace App\OurEdu\LearningResources\Repository;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface LearningResourceRepositoryInterface
{
    /**
     * @return Collection
     */
    public function all(): Collection;

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
     * @param int $id
     * @return bool
     */
    public function update(array $data): bool;

    /**
     * @param Subject $subject
     * @return bool
     */
    public function delete(): bool;


}
