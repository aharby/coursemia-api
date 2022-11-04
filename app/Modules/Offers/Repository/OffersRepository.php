<?php

namespace App\Modules\Offers\Repository;

use App\Modules\Offers\Models\Offer;
use Illuminate\Support\Collection;

class OffersRepository implements OffersRepositoryInterface
{
    protected $model;

    public function __construct(Offer $offer)
    {
        $this->model = $offer;
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

    public function find(int $id): Offer|null
    {

        return $this->model->find($id);
    }

    public function create(array $attributes): Offer
    {

        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {

        $model = $this->model->find($id);
        $courses = request()->selected_courses;
        $model->offerCourses()->sync($courses);
        return $model->update($attributes);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }

    public function pluck(): Collection
    {
        return $this->model->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }
}
