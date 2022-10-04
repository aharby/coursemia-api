<?php

namespace App\OurEdu\PsychologicalTests\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;

class PsychologicalOptionRepository implements PsychologicalOptionRepositoryInterface
{
    private $psychoOption;

    public function __construct(PsychologicalOption $psychoOption)
    {
        $this->psychoOption = $psychoOption;
    }

    public function setPsychologicalOption($psychoOption)
    {
        $this->psychoOption = $psychoOption;

        return $this;
    }
    

    /**
     * @return LengthAwarePaginator
     */
    public function all($testId): LengthAwarePaginator
    {
        return $this->psychoOption->where('psychological_test_id', $testId)->jsonPaginate(env('PAGE_LIMIT', 20));
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
        return $this->psychoOption->latest()->where('psychological_test_id', $testId)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param array $data
     * @return PsychologicalOption
     */
    public function create($testId, array $data): PsychologicalOption
    {
        $data['psychological_test_id'] = $testId;
        
        return $this->psychoOption->create($data);
    }

    /**
     * @param array $data
     * @return PsychologicalOption|null
     */
    public function update(array $data): ?PsychologicalOption
    {
        if ($this->psychoOption->update($data)) {
            return $this->psychoOption->find($this->psychoOption->id);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->psychoOption->delete();
    }

    public function findOrFail($id): ?PsychologicalOption
    {
        return PsychologicalOption::findOrFail($id);
    }
}
