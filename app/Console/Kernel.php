<?php

namespace App\Console;

use App\Modules\BaseApp\Jobs\CleanNotificationJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('coursesAndSubjects:outOfDate')->daily();
//        $schedule->command('vcrSession:notify-absents')->everyMinute();
        $schedule->command('passport:keys')->dailyAt('17:20:00');
        $schedule->command('import:class-sessions')->everyMinute()->onOneServer();
        $schedule->command('homeworkAndPeriodicTest:notify-homeworkAndPeriodicTest-students')->everyThirtyMinutes()->onOneServer();
        $schedule->command('vcrSession:notify-session-time')->everyFifteenMinutes()->onOneServer();
        $schedule->command('zoom:records')->everyThirtyMinutes()->onOneServer();
        // $schedule->command('handle:vcrProvider')->everyFifteenMinutes()->onOneServer();
        $schedule->command('prepare-exam-question-done')->dailyAt('00:00:00')->onOneServer();
        $schedule->command('generalExam:report')->everyFourHours()->onOneServer();
        //$schedule->command('vcrSession:prepare')->everyMinute()->onOneServer();
//        $schedule->command('telescope:prune')->daily();
//        if (App::environment('production')) {
//
//            $schedule->command('backup:clean')->daily()->at('01:00');
//            $schedule->command('backup:run')->daily()->at('02:00');
//        }
        $schedule->command('generalQuiz:notify-generalQuiz-time')->everyThirtyMinutes()->onOneServer();
//        $schedule->command('cleanup-notification')->hourly()->onOneServer();
        $schedule->job(CleanNotificationJob::class)->dailyAt('00:00:00')->onOneServer();
        $schedule->command('payment:check_failed')->everyThirtyMinutes()->onOneServer();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
