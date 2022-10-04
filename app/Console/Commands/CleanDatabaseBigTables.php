<?php

namespace App\Console\Commands;

use App\OurEdu\Notifications\Models\Notification;
use Illuminate\Console\Command;
use Spatie\LaravelQueuedDbCleanup\CleanDatabaseJobFactory;

class CleanDatabaseBigTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CleanDatabaseJobFactory::new()
            ->query(Notification::query()->where('created_at', '<',  now()->subMonth()))
            ->deleteChunkSize(100)
            ->dispatch();
    }
}
