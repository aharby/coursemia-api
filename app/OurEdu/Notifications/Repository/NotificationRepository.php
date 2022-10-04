<?php

namespace App\OurEdu\Notifications\Repository;

use App\OurEdu\Notifications\Enums\NotifiableTypesEnum;
use App\OurEdu\Notifications\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    protected $model;

    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return $this->model->where('notifiable_type', NotifiableTypesEnum::USER_TYPE)
            ->where('notifiable_id', auth()->id())
            ->jsonPaginate();
    }

    /**
     * @param  int  $id
     * @return Notification|null
     */
    public function findOrFail(int $id): ?Notification
    {
        return $this->model->findOrFail($id);
    }
}
