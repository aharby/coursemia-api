<?php

namespace App\OurEdu\GradeClasses\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Option;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GradeClassRepository implements GradeClassRepositoryInterface
{

    use Filterable;
    protected $model;

    public function __construct(GradeClass $gradeClass)
    {
        $this->model = $gradeClass;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return $this->model->orderBy('id','DESC')->paginate(env('PAGE_LIMIT', 20));
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
        $this->model = $this->applyFilters($this->model , $filters);
        return $this->model->orderBy('id','DESC')->paginate($perPage,['*'], $pageName, $page = null);
    }

    public function getByGradeClassesIds($gradeClassIds = [])
    {
        return  $this->model
            ->whereIn('id',$gradeClassIds)
            ->get();
    }

    /**
     * @param int $id
     * @return GradeClass|null
     */
    public function findOrFail(int $id): ?GradeClass
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return GradeClass
     */
    public function create(array $data): GradeClass
    {
        return $this->model->create($data);
    }

    /**
     * @param GradeClass $gradeClass
     * @param array $data
     * @return bool
     */
    public function update(GradeClass $gradeClass, array $data): bool
    {
        return $gradeClass->update($data);
    }

    /**
     * @param GradeClass $gradeClass
     * @return bool
     * @throws \Exception
     */
    public function delete(GradeClass $gradeClass): bool
    {
        return $gradeClass->delete();
    }

    /**
     * @return Array
     */
    public function pluck(): Array
    {
        return $this->model->with('translations')->listsTranslations('title')->pluck('title', 'id')->toArray();
    }

    public function pluckByCountryId(int $countryId): Collection
    {
        return $this->model->where('country_id',$countryId)->with('translations')->listsTranslations('title')->pluck('title', 'id');
    }
}
