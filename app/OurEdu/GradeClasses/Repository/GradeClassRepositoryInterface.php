<?php

declare(strict_types=1);

namespace App\OurEdu\GradeClasses\Repository;

use App\OurEdu\GradeClasses\GradeClass;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface GradeClassRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all() : LengthAwarePaginator;

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = [],$perPage = null, $pageName = 'page', $page = null) : LengthAwarePaginator;

    /**
     * @param int $id
     * @return GradeClass|null
     */
    public function findOrFail(int $id): ?GradeClass;

    /**
     * @param array $data
     * @return GradeClass
     */
    public function create(array $data): GradeClass;

    /**
     * @param GradeClass $gradeClass
     * @param array $data
     * @return bool
     */
    public function update(GradeClass $gradeClass, array $data): bool;

    /**
     * @param GradeClass $gradeClass
     * @return bool
     */
    public function delete(GradeClass $gradeClass): bool;

    /**
     * @return Array
     */
    public function pluck(): Array;

    public function pluckByCountryId(int $countryId) : Collection;
}
