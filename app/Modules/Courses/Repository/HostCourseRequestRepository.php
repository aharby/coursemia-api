<?php


namespace App\Modules\Courses\Repository;

use App\Modules\Courses\Models\CourseFlashcard;
use App\Modules\Courses\Models\HostCourseRequest;
use App\Modules\Events\Models\Event;
use Illuminate\Support\Collection;

class HostCourseRequestRepository implements HostCourseRequestRepositoryInterface
{
    protected $model;

    public function __construct(HostCourseRequest $hostCourseRequest)
    {
        $this->model = $hostCourseRequest;
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
            ->paginate(request()->perPage, ['*'], 'page', request()->page);
    }

    public function find(int $id): HostCourseRequest|null
    {

        return $this->model->find($id);
    }

    public function create(array $attributes): HostCourseRequest
    {

        return $this->model->create($attributes);
    }
}
