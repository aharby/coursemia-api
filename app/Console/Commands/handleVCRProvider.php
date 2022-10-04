<?php

namespace App\Console\Commands;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\Jobs\VcrSessionProvider;
use Illuminate\Console\Command;

class handleVCRProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:vcrProvider';

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
        $now = now()->subMinute()->toDateTimeString();
        $time = now()->addMinutes(29)->toDateTimeString();
        $wheres = [
            ['time_to_start', '>=', $now],
            ['time_to_start', '<=', $time],
        ];

        // school vcr sessions
        $schoolVCRSessions = VCRSession::where($wheres)
            ->with('instructor')
            ->whereNotNull('instructor_id')
            ->whereNotNull('subject_id')
            ->whereHas('instructor')
            ->where('vcr_session_type', VCRSessionEnum::SCHOOL_SESSION)
            ->where('meeting_type', null)
            ->get();
        foreach ($schoolVCRSessions as $vcrSession) {
            VcrSessionProvider::dispatch($vcrSession)->onQueue('sessions');
        }
    }
}
