<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\Jobs;

use App\OurEdu\GradeClasses\GradeClass;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\OurEdu\SchoolAccounts\Classroom;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;

class SaveFormativeTestClassroomJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Collection
     */
    private $formativeTests;
    /**
     * @var GeneralQuiz
     */
    private $classroom;
    private GeneralQuizRepositoryInterface $generalQuizRepo;
    private GradeClass $gradeClass;

    /**
     * @param GradeClass $gradeClass
     * @param Classroom $classroom
     */
    public function __construct(GradeClass $gradeClass, Classroom $classroom)
    {
        $this->classroom = $classroom;
        $this->generalQuizRepo = app(GeneralQuizRepositoryInterface::class);
        $this->gradeClass = $gradeClass;
    }

    public function handle()
    {
        $this->formativeTests = $this->generalQuizRepo->getGeneralQuizByGradeClass($this->gradeClass->id);

        foreach ($this->formativeTests as $formativeTest) {
            $this->generalQuizRepo->saveNewlyGeneralQuizClassrooms(
                $formativeTest,
                $this->classroom->id
            );
        }
    }
}
