<?php

declare(strict_types=1);
namespace App\OurEdu\PsychologicalTests\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;

interface PsychologicalQuestionRepositoryInterface
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
     * @return PsychologicalQuestion|null
     */
    public function findOrFail(int $id): ?PsychologicalQuestion;

    /**
     * @param array $data
     * @return PsychologicalQuestion
     */
    public function create($testId, array $data): PsychologicalQuestion;

    /**
     * @param array $data
     * @return PsychologicalQuestion|null
     */
    public function update(array $data): ?PsychologicalQuestion;

    /**
     * @param PsychologicalQuestion $subject
     * @return bool
     */
    public function delete(): bool;
}
