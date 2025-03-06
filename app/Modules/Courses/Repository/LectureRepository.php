<?php


namespace App\Modules\Courses\Repository;

use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Events\Models\Event;
use Illuminate\Support\Collection;

use App\Modules\Courses\Models\LectureProgress;

class LectureRepository implements LectureRepositoryInterface
{
    protected $model;

    public function __construct(CourseLecture $lecture)
    {
        $this->model = $lecture;
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

    public function find(int $id): Event|null
    {

        return $this->model->find($id);
    }

    public function create(array $attributes): Event
    {

        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {

        return $this->model->find($id)->update($attributes);
    }

    public function delete(int $id): bool
    {
        $event = $this->model->find($id);
        if ($event) {
            return $event->delete();
        }
        return false;
    }

    public function pluck(): Collection
    {
        return $this->model->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }

    public function updateLastPosition($user_id, $lecture_id, $position)
    {
        return LectureProgress::updateOrCreate(
            ['user_id' => $user_id, 'lecture_id' => $lecture_id],
            ['last_position' => $position]
        );
    }

}
