<?php

namespace App\Console\Commands;

use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Enums\ImportJobsStatusEnums;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Imports\ScheduledSessionImports;
use Exception;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportScheduledSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:class-sessions';

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
        $file = ImportJob::query()->where("status", "=", ImportJobsStatusEnums::IN_PROGRESS)->first();
        if (!$file) {
            $file = ImportJob::query()->where("status", "=", ImportJobsStatusEnums::PENDING)->first();
            if ($file) {
                Excel::import(new ScheduledSessionImports($file), storage_path() . "/app/public/" . $file->filename);
            }
        }
    }
}
