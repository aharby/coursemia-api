<?php

declare(strict_types=1);
namespace App\OurEdu\PsychologicalTests\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;

interface PsychologicalOptionRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all($testId): LengthAwarePaginator;

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($testId, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    /**
     * @param int $id
     * @return PsychologicalOption|null
     */
    public function findOrFail(int $id): ?PsychologicalOption;

    /**
     * @param array $data
     * @return PsychologicalOption
     */
    public function create($testId, array $data): PsychologicalOption;

    /**
     * @param array $data
     * @return PsychologicalOption|null
     */
    public function update(array $data): ?PsychologicalOption;

    /**
     * @param PsychologicalOption $subject
     * @return bool
     */
    public function delete(): bool;
}
