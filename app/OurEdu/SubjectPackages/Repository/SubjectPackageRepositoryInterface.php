<?php

declare(strict_types=1);

namespace App\OurEdu\SubjectPackages\Repository;

use App\OurEdu\SubjectPackages\Package;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubjectPackageRepositoryInterface
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
     * @return Package|null
     */
    public function findOrFail(int $id): ?Package;

    /**
     * @param array $data
     * @return Package
     */
    public function create(array $data): Package;

    /**
     * @param array $data
     * @param int $id
     * @return Package
     */
    public function update(array $data): Package;

    /**
     * @param Package $package
     * @return bool
     */
    public function delete(): bool;

    public function attachSubjects(array $ids);

    public function getSubjectsIds(): array;

    public function getSubjects();

    public function syncSubjects(array $ids);

    public function paginateWhereStudent(
        array $studentData,
        $perPage = null,
        $pageName = 'page',
        $page = null
    ): LengthAwarePaginator;
}
