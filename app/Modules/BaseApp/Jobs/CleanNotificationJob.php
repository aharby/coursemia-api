<?php

namespace App\Modules\BaseApp\Jobs;

use App\Modules\BaseApp\Jobs\Middleware\AtomicJobMiddleware;
use App\Modules\Notifications\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanNotificationJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function handle()
    {
        $numberOfRecordsDeleted = Notification::query()
            ->where('created_at', '<', now()->subMonth())
            ->limit(10000)
            ->delete();

        if ($numberOfRecordsDeleted > 0) {
            self::dispatch(static::class);
        }
    }

    public function middleware()
    {
        return [new AtomicJobMiddleware()];
    }
}
