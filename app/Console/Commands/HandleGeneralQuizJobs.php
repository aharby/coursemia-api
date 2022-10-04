<?php

namespace App\Console\Commands;

use App\OurEdu\BaseNotification\Jobs\CalculateGeneralAverageGradesAndCountStudentsJob;
use App\OurEdu\BaseNotification\Jobs\FinishGeneralQuizStudentJob;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Homework\Jobs\NotificationHomeworkStudentsJob;
use App\OurEdu\GeneralQuizzes\Jobs\UpdateStudentOrderJob;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HandleGeneralQuizJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generalQuiz:notify-generalQuiz-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generalQuiz:notify-generalQuiz-time';

    /**
     * Create a new command instance.
     * @return void
     */

    // local variables
    private $notifierFactory;
    private GeneralQuizRepositoryInterface $generalQuizRepository;

    public function __construct(
        NotifierFactoryInterface $notifierFactory,
        GeneralQuizRepositoryInterface $generalQuizRepository
    ) {
        parent::__construct();
        $this->notifierFactory = $notifierFactory;
        $this->generalQuizRepository = $generalQuizRepository;
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $now = now()->subMinute()->toDateTimeString();
        $time = now()->addMinutes(29)->toDateTimeString();
        $intervals = [
            ['start_at', '>=', $now],
            ['start_at', '<=', $time],
        ];

        $upComingGeneralQuizzes = GeneralQuiz::where($intervals)
            ->where('is_notified', 0)
            ->whereNotNull('published_at')
            ->whereIn('quiz_type', [GeneralQuizTypeEnum::HOMEWORK, GeneralQuizTypeEnum::PERIODIC_TEST,GeneralQuizTypeEnum::FORMATIVE_TEST])
            ->get();

        if (!$upComingGeneralQuizzes->isEmpty()) {
            $this->homeWorkAndPeriodicTestHandleJobs($upComingGeneralQuizzes);
        }

        $finishedGeneralQuizzes = GeneralQuiz::whereBetween('end_at', [$now,$time])
            ->where('finished_check', 0)
            ->whereNotNull('published_at')
            ->whereIn('quiz_type', [GeneralQuizTypeEnum::HOMEWORK, GeneralQuizTypeEnum::PERIODIC_TEST,GeneralQuizTypeEnum::FORMATIVE_TEST])
            ->get();

        if (!$finishedGeneralQuizzes->isEmpty()) {
            $this->homeWorkAndPeriodicTestHandleFinishJobs($finishedGeneralQuizzes);
        }

        return 0;
    }

    private function notifyStudents($generalQuiz)
    {
        if ((new Carbon($generalQuiz->start_at))->isFuture()) {
            NotificationHomeWorkStudentsJob::dispatch($generalQuiz)->delay((new Carbon($generalQuiz->start_at)));
            return;
        }

        NotificationHomeworkStudentsJob::dispatch($generalQuiz);
    }
    private function homeWorkAndPeriodicTestHandleJobs($generalQuizzes)
    {
        foreach ($generalQuizzes as $generalQuiz) {
            $this->notifyStudents($generalQuiz);
            $generalQuiz->update(['is_notified' => 1]);
        }
    }


    private function homeWorkAndPeriodicTestHandleFinishJobs($generalQuizzes)
    {
        foreach ($generalQuizzes as $generalQuiz) {
            $carbonEndAt = new Carbon($generalQuiz->end_at);

            FinishGeneralQuizStudentJob::dispatch($generalQuiz)->delay(
                $carbonEndAt->addMinute()
            );
            CalculateGeneralAverageGradesAndCountStudentsJob::dispatch($generalQuiz)->delay(
                $carbonEndAt->addMinutes(5)
            );

            UpdateStudentOrderJob::dispatch($generalQuiz)->delay($carbonEndAt->addMinutes(5));

            $generalQuiz->update(['finished_check' => 1]);
        }
    }
}
