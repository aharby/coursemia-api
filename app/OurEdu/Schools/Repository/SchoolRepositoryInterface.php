<?php

declare(strict_types=1);

namespace App\OurEdu\Schools\Repository;

use App\OurEdu\Schools\School;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SchoolRepositoryInterface
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
    public function paginate(array $filters ,$perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;


    /**
     * @param int $id
     * @return School|null
     */
    public function findOrFail(int $id): ?School;


    /**
     * @param array $data
     * @return School
     */
    public function create(array $data): School;


    /**
     * @param School $gradeClass
     * @param array $data
     * @return bool
     */
    public function update(School $gradeClass, array $data): bool;


    /**
     * @param School $gradeClass
     * @return bool
     */
    public function delete(School $gradeClass): bool;


    public function pluck(): Collection;
}
