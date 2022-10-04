<?php

declare(strict_types=1);
namespace App\OurEdu\PsychologicalTests\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;

interface PsychologicalTestRepositoryInterface
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
     * @param int $id
     * @return PsychologicalTest|null
     */
    public function findOrFail(int $id): ?PsychologicalTest;

    /**
     * @param array $data
     * @return PsychologicalTest
     */
    public function create(array $data): PsychologicalTest;

    /**
     * @param array $data
     * @return PsychologicalTest|null
     */
    public function update(array $data): ?PsychologicalTest;

    /**
     * @param PsychologicalTest $subject
     * @return bool
     */
    public function delete(): bool;
}
