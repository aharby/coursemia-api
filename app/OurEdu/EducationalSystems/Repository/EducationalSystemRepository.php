<?php
namespace App\OurEdu\EducationalSystems\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\SchoolAccounts\SchoolAccountEducationalSystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EducationalSystemRepository implements EducationalSystemRepositoryInterface
{
    use Filterable;

    protected $model;
    public function __construct(EducationalSystem $educationalSystem) {
        $this->model = $educationalSystem;
    }
    public function all(array $filters = []): LengthAwarePaginator
    {
        $this->model = $this->applyFilters($this->model , $filters);
        return $this->model->orderBy('id','DESC')->jsonPaginate(env('PAGE_LIMIT',20));
    }

    public function find(int $id) : EducationalSystem {

        return $this->model->find($id);
    }
    public function create(array $attributes) : EducationalSystem {

        return $this->model->create($attributes);
    }
    public function update(int $id , array $attributes) : bool {

        return $this->model->find($id)->update($attributes);
    }
    public function delete(int $id) : bool {

        return $this->model->find($id)->delete();
    }

    public function pluck(): Collection
    {
        return $this->model->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }

    public function pluckByCountryId(int $countryId): Collection
    {
        return $this->model->where('country_id',$countryId)->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }

    public function pluckBySchoolAccountId($schoolAccountId)
    {
        $educationalSystemIds = SchoolAccountEducationalSystem::where('school_account_id',$schoolAccountId)->pluck('educational_system_id');
        return $this->model->whereIn('educational_systems.id',$educationalSystemIds)->with('translations')->listsTranslations('name')->pluck('name', 'id');

    }
}
