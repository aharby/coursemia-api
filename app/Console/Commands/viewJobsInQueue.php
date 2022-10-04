<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class viewJobsInQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:jobs:onQueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View Jobs on Queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queueName = $this->ask('For any redis queue');
        $this->info('Ok the count is '.\Queue::connection('redis')->size($queueName));
        $queues = \Queue::getRedis()->zrange('queues:'.$queueName.':delayed', 0, -1);
        $i = 0;
        $queuesData = [];
        foreach ($queues as $queue) {
            try {
                $payload = (json_decode($queue));
                $delay = unserialize($payload->data->command)->delay;
                if (!is_null($delay)) {
                    $queuesData['delayOn'][$payload->displayName] = [
                            'count' => isset($queuesData['delayOn'][$payload->displayName]['count']) ? $queuesData['delayOn'][$payload->displayName]['count'] + 1 : 1,
                            'isFutureCount' => isset($queuesData['delayOn'][$payload->displayName]['isFutureCount']) ? $queuesData['delayOn'][$payload->displayName]['isFutureCount'] + 1 : 1,
                        ];
                } else {
                    $queuesData['delayOff'][$payload->displayName] = [
                            'count' => isset($queuesData['delayOff'][$payload->displayName]['count']) ? $queuesData['delayOff'][$payload->displayName]['count'] + 1 : 1,
                            'queueId' => isset($queuesData['delayOff'][$payload->displayName]['queueId']) ? $queuesData['delayOff'][$payload->displayName]['queueId'] .','.$payload->uuid : $payload->uuid
                    ];
                }
            } catch (\Throwable $exception) {
                dump($exception->getMessage());
                dump(unserialize($payload->data->command));
                $false++;
                if ($this->confirm('Do you wish to continue?')) {
                    continue;
                }
                break;
            }
        }
        dump($queuesData);
    }
}
