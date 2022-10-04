<?php

namespace App\OurEdu\LearningResources\Repository;

use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Subjects\Models\Subject;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LearningResourceRepository implements LearningResourceRepositoryInterface
{

    private $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }


    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->resource->all();
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
        return $this->subject->latest()->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->subject->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param int $id
     * @return Subject|null
     */
    public function findOrFail(int $id): ?Subject
    {
        return $this->subject->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Subject
     */
    public function create(array $data): Subject
    {
        return $this->subject->create($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        return $this->subject->update($data);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->subject->delete();
    }

    /**
     * @param $param
     * @param $val
     * @return Resource|null
     */
    public function findResourceBy($param,$val): ?Resource
    {
        return $this->resource->where($param,$val)->first();
    }
}
