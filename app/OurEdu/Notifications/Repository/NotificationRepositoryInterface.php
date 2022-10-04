<?php

namespace App\OurEdu\Notifications\Repository;

use App\OurEdu\Notifications\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;


    /**
     * @param  int  $id
     * @return Notification|null
     */
    public function findOrFail(int $id): ?Notification;
}
