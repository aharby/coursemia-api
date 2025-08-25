<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeHash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:hash {value}';

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
        $value = $this->argument('value');
        $this->info("Hashing value: {$value}");
        $hash = \Illuminate\Support\Facades\Hash::make($value);
        $this->info($hash);

        return 0;
    }
}
