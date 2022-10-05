<?php

namespace App\Modules\BaseApp\Jobs\Middleware;

use Illuminate\Cache\RedisLock;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Cache;

class AtomicJobMiddleware
{
    /**
     * Process the queued job.
     *
     * @param Job $job
     * @param callable $next
     */
    public function handle($job, $next)
    {
        /** @var RedisLock $lock */
        $lock = Cache::store('redis')->lock("{$job->resolveName()}_lock", 10 * 60);

        if (!$lock->get()) {
            $job->delete();

            return;
        }

        $next($job);

        $lock->release();
    }
}
