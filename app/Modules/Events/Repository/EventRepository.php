<?php


namespace App\Modules\Events\Repository;

use App\Modules\Events\Models\Event;
use App\Modules\Events\Repository\EventRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventRepository implements EventRepositoryInterface
{
    protected $model;

    public function __construct(Event $event)
    {
        $this->model = $event;
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
}
