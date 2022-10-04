<?php

declare(strict_types=1);
namespace App\OurEdu\PsychologicalTests\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

interface PsychologicalRecomendationRepositoryInterface
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
     * @return PsychologicalRecomendation|null
     */
    public function findOrFail(int $id): ?PsychologicalRecomendation;

    /**
     * @param array $data
     * @return PsychologicalRecomendation
     */
    public function create($testId, array $data): PsychologicalRecomendation;

    /**
     * @param array $data
     * @return PsychologicalRecomendation|null
     */
    public function update(array $data): ?PsychologicalRecomendation;

    /**
     * @param PsychologicalRecomendation $subject
     * @return bool
     */
    public function delete(): bool;
}
