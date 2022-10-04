<?php

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\VCRSchedules\Models\VCRRequest;

class VCRRequestRepository implements VCRRequestRepositoryInterface
{

    use Filterable;
    protected $model;

    public function __construct(VCRRequest $VCRRequest)
    {
        $this->model = $VCRRequest;
    }


    /**
     * @param int $id
     * @return VCRRequest|null
     */
    public function findOrFail(int $id): ?VCRRequest
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return VCRRequest
     */
    public function create(array $data): VCRRequest
    {
        return $this->model->create($data);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function getInstructorRequests($instructorID)
    {
        return $this->model->where('instructor_id' , $instructorID)
            ->orderBy('id' , 'desc')
            ->jsonPaginate(env('PAGE_LIMIT', 10));
    }

    public function update(int $id , array $data) : bool
    {
        $model = $this->model->findOrFail($id);
        return $model->update($data);
    }


}

