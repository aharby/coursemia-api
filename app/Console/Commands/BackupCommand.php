<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Exception;

class BackupCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database and uploads';
    protected $process;

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
     * @return mixed
     */
    public function handle()
    {
        try {
            $today = today()->format('Y-m-d');
            $path = 'backups/' . $today;
            if (!File::isDirectory(storage_path() . '/backups')) {
                File::makeDirectory(storage_path() . '/backups', 0777, true, true);
            }
            if (!File::isDirectory(storage_path() . '/' . $path)) {
                File::makeDirectory(storage_path() . '/' . $path, 0777, true, true);
            }
            $db = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            /// backup database
            $command = "mysqldump --compact --skip-comments -u {$username} --password={$password} {$db} > " . storage_path($path) . '/' . $db . '.sql';
            $this->process = new Process($command);
            $this->process->mustRun();
            ////////////////////////////////////////////// backup attachment
            $uploadsName = 'uploads';
            $compressedPath = storage_path() . '/' . $path . '/' . $uploadsName . '.tar.gz';
            $command = "tar -C " . public_path() . " -czvf " . $compressedPath . ' ' . $uploadsName;
            $this->process = new Process($command);
            $this->process->mustRun();
        } catch (Exception $exception) {
            Log::error('Error: ' . $exception->getMessage() . ', File: ' . $exception->getFile() . ', Line:' . $exception->getLine() . PHP_EOL);
        }
        return 0;

    }

}
