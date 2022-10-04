<?php

namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Jobs;

use App\OurEdu\BaseApp\Enums\V2\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\OurEdu\Users\User;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepository;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
class UpdateStudentPeriodicTestTimeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;
    /**
     * @var GeneralQuiz
     */
    private $periodicTest;

    /**
     * @var GeneralQuizStudentRepository
     */
    private $studentGeneralQuizRepo;
    private $studentTestDuration;

    /**
     * UpdateStudentPeriodicTestTimeJob constructor.
     * @param User $user
     * @param GeneralQuizStudentRepository $studentGeneralQuizRepo
     * @param GeneralQuiz $periodicTest
     */
    public function __construct(
        GeneralQuiz $periodicTest,
        GeneralQuizStudentRepository $studentGeneralQuizRepo,
        User $user,$studentTestDuration
    )
    {
        $this->user = $user;
        $this->periodicTest = $periodicTest;
        $this->studentGeneralQuizRepo = $studentGeneralQuizRepo;
        $this->studentTestDuration = $studentTestDuration;
    }

    public function handle()
    {
        if ($this->user && !$this->periodicTest->is_finished) {
            $studentGeneralQuiz = $this->studentGeneralQuizRepo->findStudentGeneralQuiz(
                $this->periodicTest->id, $this->user->id
            );
            if($studentGeneralQuiz){
                if($studentGeneralQuiz->student_test_duration > 0){
                    $studentGeneralQuiz->student_test_duration = $studentGeneralQuiz->student_test_duration - $this->studentTestDuration;
                    $studentGeneralQuiz->save();
                }
                return true;
            }
        }
        return false;
    }
}
