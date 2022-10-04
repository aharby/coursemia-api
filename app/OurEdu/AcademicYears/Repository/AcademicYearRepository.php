<?php


namespace App\OurEdu\AcademicYears\Repository;


use App\OurEdu\AcademicYears\AcademicYear;
use Illuminate\Pagination\LengthAwarePaginator;

class AcademicYearRepository implements AcademicYearRepositoryInterface
{

    protected $model;
    public function __construct(AcademicYear $academicYear) {
        $this->model = $academicYear;
    }

    public function all(): LengthAwarePaginator
    {
        return $this->model->orderBy('id','DESC')->paginate(env('PAGE_LIMIT',20));
    }
    public function find(int $id) : AcademicYear {

        return $this->model->find($id);
    }
    public function create(array $attributes) : AcademicYear {

        return $this->model->create($attributes);
    }
    public function update(int $id, array $attributes) : bool {

        return $this->model->find($id)->update($attributes);
    }
    public function delete(int $id) : bool {

        return $this->model->find($id)->delete();
    }

}
