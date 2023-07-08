<?php


namespace App\Modules\Courses\Repository;

use App\Modules\Courses\Models\CourseFlashcard;
use App\Modules\Events\Models\Event;
use Illuminate\Support\Collection;

class FlashCardRepository implements FlashCardRepositoryInterface
{
    protected $model;

    public function __construct(CourseFlashcard $flashcard)
    {
        $this->model = $flashcard;
    }

    public function all($isActive = false)
    {
        $query = $this->model
            ->query();
        $query = $query->whereIn('admin_id', [request()->header('Admin-Id'),1]);
        if ($isActive) {
            $query->active();
        }
        return $query
            ->filter()
            ->sorter()
            ->paginate(request()->perPage, ['*'], 'page', request()->page);
    }

    public function find(int $id): CourseFlashcard|null
    {

        return $this->model->find($id);
    }

    public function create(array $attributes): CourseFlashcard
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
}
