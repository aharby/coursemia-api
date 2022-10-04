<?php


namespace App\OurEdu\Countries\Repository;


use App\OurEdu\Countries\Country;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CountryRepository implements CountryRepositoryInterface
{
    protected $model;

    public function __construct(Country $country)
    {
        $this->model = $country;
    }

    public function all(): LengthAwarePaginator
    {

        return $this->model->orderBy('id','DESC')->paginate(env('PAGE_LIMIT', 20));
    }

    public function find(int $id): Country
    {

        return $this->model->find($id);
    }

    public function create(array $attributes): Country
    {

        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {

        return $this->model->find($id)->update($attributes);
    }

    public function delete(int $id): bool
    {

        return $this->model->find($id)->delete();
    }

    public function pluck(): Collection
    {
        return $this->model->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }
}
