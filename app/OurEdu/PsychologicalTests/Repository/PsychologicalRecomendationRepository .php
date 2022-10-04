<?php

namespace App\OurEdu\PsychologicalTests\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

class PsychologicalRecomendationRepository implements PsychologicalRecomendationRepositoryInterface
{
    private $psychoQuestion;

    public function __construct(PsychologicalRecomendation $psychoQuestion)
    {
        $this->psychoQuestion = $psychoQuestion;
    }

    public function setPsychologicalRecomendation($psychoQuestion)
    {
        $this->psychoQuestion = $psychoQuestion;

        return $this;
    }
    

    /**
     * @return LengthAwarePaginator
     */
    public function all($testId): LengthAwarePaginator
    {
        return $this->psychoQuestion->where('psychological_test_id', $testId)->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($testId, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->psychoQuestion->latest()->where('psychological_test_id', $testId)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param array $data
     * @return PsychologicalRecomendation
     */
    public function create($testId, array $data): PsychologicalRecomendation
    {
        $data['psychological_test_id'] = $testId;
        
        return $this->psychoQuestion->create($data);
    }

    /**
     * @param array $data
     * @return PsychologicalRecomendation|null
     */
    public function update(array $data): ?PsychologicalRecomendation
    {
        if ($this->psychoQuestion->update($data)) {
            return $this->psychoQuestion->find($this->psychoQuestion->id);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->psychoQuestion->delete();
    }

    public function findOrFail($id): ?PsychologicalRecomendation
    {
        return PsychologicalRecomendation::findOrFail($id);
    }
}
