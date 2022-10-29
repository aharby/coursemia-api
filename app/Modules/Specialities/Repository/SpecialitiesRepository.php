<?php

namespace App\Modules\Specialities\Repository;

use App\Modules\Specialities\Models\Speciality;
use Illuminate\Support\Collection;

class SpecialitiesRepository implements SpecialitiesRepositoryInterface
{
    protected $model;

    public function __construct(Speciality $speciality)
    {
        $this->model = $speciality;
    }

    public function all($isActive = false)
    {
        $query = $this->model
            ->query();
        if ($isActive) {
            $query->active();
        }
        return $query
            ->filter()
            ->sorter()
            ->paginate(request()->perPage, ['*'], 'page', request()->page);
    }

    public function find(int $id): Speciality|null
    {

        return $this->model->find($id);
    }

    public function create(array $attributes): Speciality
    {

        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {

        return $this->model->find($id)->update($attributes);
    }

    public function delete(int $id): bool
    {
        $speciality = $this->model->find($id);
        if ($speciality) {
            return $speciality->delete();
        }
        return false;
    }

    public function pluck(): Collection
    {
        return $this->model->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }
}
