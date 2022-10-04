<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\Job;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneQuestionsUseCase\CloneQuestionsUseCase;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneQuestionsUseCase\CloneQuestionsUseCaseInterface;
use App\OurEdu\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloneQuestionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public GeneralQuiz $generalQuiz, public $questions, public User $creator)
    {
    }

    public function handle(CloneQuestionsUseCaseInterface $cloneQuestionsUseCase)
    {
        $cloneQuestionsUseCase->clone($this->generalQuiz, $this->questions, $this->creator);
    }
}
