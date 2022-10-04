<?php

namespace App\OurEdu\Schools\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Schools\School;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SchoolRepository implements SchoolRepositoryInterface
{
    use Filterable;

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return School::orderBy('id','DESC')->jsonPaginate(env('PAGE_LIMIT', 20));
    }


    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = [], $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);

        $model = $this->applyFilters(new School() , $filters);
        return $model->orderBy('id','DESC')->jsonPaginate($perPage,['*'], $pageName, $page = null);
    }


    /**
     * @param int $id
     * @return School|null
     */
    public function findOrFail(int $id): ?School
    {
        return School::findOrFail($id);
    }


    /**
     * @param array $data
     * @return School
     */
    public function create(array $data): School
    {
        return School::create($data);
    }


    /**
     * @param School $school
     * @param array $data
     * @return bool
     */
    public function update(School $school, array $data): bool
    {
        return $school->update($data);
    }


    /**
     * @param School $school
     * @return bool
     * @throws \Exception
     */
    public function delete(School $school): bool
    {
        return $school->delete();
    }

    public function pluck(): Collection
    {
        return School::with('translations')->listsTranslations('name')->pluck('name', 'id');
    }
}
