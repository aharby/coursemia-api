<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateLectureDurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lectures:update-duration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update lecture durations from video URLs';

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
        $this->info("Updating lecture durations...");

        $lectures = \App\Modules\Courses\Models\CourseLecture::whereNotNull('url') ->whereNull('duration_seconds')->get();

        foreach ($lectures as $lecture) {

            $videoService = new \App\Modules\Courses\Services\VideoService();
            
            try {
                $duration = $videoService->getVideoDuration($lecture->url);

                if ($duration !== null) {

                    $lecture->update(['duration_seconds' => $duration]);

                    $this->line("✔ Updated lecture ID {$lecture->id} with duration {$duration} seconds");
                } else {
                    $this->warn("⚠ Skipped lecture ID {$lecture->id} (no duration found)");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error with lecture ID {$lecture->id}: " . $e->getMessage());
            }

        }

        return 0;
    }
}
