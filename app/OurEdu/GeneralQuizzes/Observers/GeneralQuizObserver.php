<?php

namespace App\OurEdu\GeneralQuizzes\Observers;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use Carbon\Carbon;

class GeneralQuizObserver
{
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;

    /**
     * GeneralQuizObserver constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
    }

    /**
     * Handle the general quiz "updated" event.
     *
     * @param  GeneralQuiz  $generalQuiz
     * @return void
     */
    public function updated(GeneralQuiz $generalQuiz)
    {
    }
}
