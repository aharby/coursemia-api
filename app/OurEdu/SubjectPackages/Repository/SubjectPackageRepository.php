<?php

namespace App\OurEdu\SubjectPackages\Repository;

use App\OurEdu\SubjectPackages\Package;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepositoryInterface;

class SubjectPackageRepository implements SubjectPackageRepositoryInterface
{
    private $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }


    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->package->all();
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
        return $this->package->latest()->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->package->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param int $id
     * @return Package|null
     */
    public function findOrFail(int $id): ?Package
    {
        return $this->package->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Package
     */
    public function create(array $data): Package
    {
        return $this->package->create($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data): Package
    {
         $this->package->update($data);
         return $this->package;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->package->delete();
    }

    public function attachSubjects(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->package->subjects()->attach($ids);
    }

    public function getSubjectsIds(): array
    {
        return $this->package->subjects()->pluck('id')->toArray() ?? [];
    }

    public function getSubjects()
    {
        return $this->package->subjects()->get();
    }

    public function syncSubjects(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->package->subjects()->sync($ids);
    }

    public function paginateWhereStudent(
        array $studentData,
        $perPage = null,
        $pageName = 'page',
        $page = null
    ): LengthAwarePaginator {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->package
            ->where('country_id', $studentData['country_id'])
            ->where('educational_system_id', $studentData['educational_system_id'])
            ->where('academical_years_id', $studentData['academical_years_id'])
            ->where('grade_class_id', $studentData['class_id'])
            ->where('is_active', 1)
            ->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }
}
