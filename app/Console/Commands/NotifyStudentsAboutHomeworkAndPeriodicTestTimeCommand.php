<?php

namespace App\Console\Commands;

use App\OurEdu\BaseNotification\Jobs\FinishVCRSessionJob;
use App\OurEdu\BaseNotification\Jobs\NotificationHomeWorkStudentsJob;
use App\OurEdu\BaseNotification\Jobs\NotificationPeriodicTestStudentsJob;
use App\OurEdu\BaseNotification\Jobs\NotificationStudentsJob;
use App\OurEdu\BaseNotification\Jobs\NotifySupervisorAboutAbsentInstructor;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepository;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyStudentsAboutHomeworkAndPeriodicTestTimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homeworkAndPeriodicTest:notify-homeworkAndPeriodicTest-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'homeworkAndPeriodicTest:notify-homeworkAndPeriodicTest-students';

    /**
     * Create a new command instance.
     * @return void
     */

    // local variables
    private $quizRepository;

    public function __construct(
        QuizRepository $quizRepository
    )
    {
        parent::__construct();
        $this->quizRepository = $quizRepository;
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $now = now()->subMinute()->toDateTimeString();
        $time = now()->addMinutes(29)->toDateTimeString();
        $interval = [
            ['start_at', '>=', $now],
            ['start_at', '<=', $time],
        ];

        // get Current HomeWork
        $quizzes = $this->quizRepository->getReadyNotifyHomeWorkAndPeriodicTest($interval);
        // dd($quizzes);
        if (!$quizzes->isEmpty()) {
            $this->homeWorkAndPeriodicTestNotify($quizzes);
        }

    return 0;
    }

    private function homeWorkAndPeriodicTestNotify($quizzes)
    {
        foreach ($quizzes as $quiz) {
            $notified = $this->notifyStudents($quiz->students, $quiz);
            if($notified){
                $quiz->update(['is_notified'=>1]);
            }
        }
    }

    private function notifyStudents($studentsUsers, $quiz)
    {
        if($quiz->quiz_type == QuizTypesEnum::HOMEWORK)
        {
            if ((new Carbon($quiz->start_at))->isFuture()) {
                NotificationHomeWorkStudentsJob::dispatch($studentsUsers, $quiz, $this->quizRepository)->delay((new Carbon($quiz->start_at)));
            } else {
                NotificationHomeWorkStudentsJob::dispatch($studentsUsers, $quiz, $this->quizRepository);
            }
        }
        if($quiz->quiz_type == QuizTypesEnum::PERIODIC_TEST)
        {
            if ((new Carbon($quiz->start_at))->isFuture()) {
                NotificationPeriodicTestStudentsJob::dispatch($studentsUsers, $quiz, $this->quizRepository)->delay((new Carbon($quiz->start_at)));
            } else {
                NotificationPeriodicTestStudentsJob::dispatch($studentsUsers, $quiz, $this->quizRepository);
            }
        }
    }
}
