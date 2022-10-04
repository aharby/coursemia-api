<?php

namespace App\OurEdu\PsychologicalTests\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalAnswer;

class PsychologicalTestRepository implements PsychologicalTestRepositoryInterface
{
    private $psychoTest;

    public function __construct(PsychologicalTest $psychoTest)
    {
        $this->psychoTest = $psychoTest;
    }

    public function setPsychologicalTest($psychoTest)
    {
        $this->psychoTest = $psychoTest;

        return $this;
    }
    
    public function deleteOldAnswers($user, $test)
    {
        PsychologicalAnswer::where([
            'user_id'   =>  $user->id,
            'psychological_test_id'   =>  $test->id,
        ])->delete();
    }
    

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return $this->psychoTest->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->psychoTest->latest()->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->psychoTest->latest()->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param array $data
     * @return PsychologicalTest
     */
    public function create(array $data): PsychologicalTest
    {
        return $this->psychoTest->create($data);
    }

    /**
     * @param array $data
     * @return PsychologicalTest|null
     */
    public function update(array $data): ?PsychologicalTest
    {
        if ($this->psychoTest->update($data)) {
            return $this->psychoTest->find($this->psychoTest->id);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->psychoTest->delete();
    }

    public function findOrFail($id): ?PsychologicalTest
    {
        return PsychologicalTest::findOrFail($id);
    }
}
