<?php

namespace App\OurEdu\PsychologicalTests\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;

class PsychologicalQuestionRepository implements PsychologicalQuestionRepositoryInterface
{
    private $psychoQuestion;

    public function __construct(PsychologicalQuestion $psychoQuestion)
    {
        $this->psychoQuestion = $psychoQuestion;
    }

    public function setPsychologicalQuestion($psychoQuestion)
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
     * @return PsychologicalQuestion
     */
    public function create($testId, array $data): PsychologicalQuestion
    {
        $data['psychological_test_id'] = $testId;
        
        return $this->psychoQuestion->create($data);
    }

    /**
     * @param array $data
     * @return PsychologicalQuestion|null
     */
    public function update(array $data): ?PsychologicalQuestion
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

    public function findOrFail($id): ?PsychologicalQuestion
    {
        return PsychologicalQuestion::findOrFail($id);
    }


    public function getActiveQuestions($user, $test)
    {
        return $test->questions()
            ->active()
            ->with('test.activeOptions.translations')
            ->paginate(1);
    }

    public function answerTestQuestion($user, $test, $data)
    {
        return $test->answers()->create([
            'user_id'   =>  $user->id,
            'psychological_question_id'   =>  $data->question_id,
            'psychological_option_id'   =>  $data->option_id,
        ]);
    }
}
