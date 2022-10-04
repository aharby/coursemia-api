<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers\TrueFalse;

use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use Illuminate\Support\Facades\Log;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCase;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;

class TrueFalseQuestionObserver
{
    private $createUpdatePrepareQuestionUseCase;
    protected $preparedGeneralExamQuestionUseCase;
    public function __construct(CreateUpdatePrepareQuestionUseCaseInterface $createUpdatePrepareQuestionUseCase, PreperedGeneralExamQuestionUseCaseInterface $preparedGeneralExamQuestionUseCase)
    {
        $this->createUpdatePrepareQuestionUseCase=$createUpdatePrepareQuestionUseCase;
        $this->preparedGeneralExamQuestionUseCase = $preparedGeneralExamQuestionUseCase;
    }

    /**
     * Handle the true false question "created" event.
     *
     * @param   $trueFalseQuestion
     * @return void
     */
    public function created(TrueFalseQuestion $trueFalseQuestion)
    {
        if (!$trueFalseQuestion->model || $trueFalseQuestion->model == QuestionModelsEnums::EXAM) {
            $this->createUpdatePrepareQuestionUseCase->createPrepareExamQuestion($trueFalseQuestion);
            $this->preparedGeneralExamQuestionUseCase->prepareQuestion($trueFalseQuestion);
        }
    }

    /**
     * Handle the true false question "updated" event.
     *
     * @param  \App\TrueFalseQuestion  $trueFalseQuestion
     * @return void
     */
    public function updated(TrueFalseQuestion $trueFalseQuestion)
    {
    }

    /**
     * Handle the true false question "deleted" event.
     *
     * @param  \App\TrueFalseQuestion  $trueFalseQuestion
     * @return void
     */
    public function deleted(TrueFalseQuestion $trueFalseQuestion)
    {
        $prepareExamQuestion = $trueFalseQuestion->prepareExamQuestion()->first();
        if ($prepareExamQuestion) {
            $prepareExamQuestion->delete();
        }

        $preparedGeneralExamQuestion = $trueFalseQuestion->preparedGeneralExamQuestion()->first();
        if ($preparedGeneralExamQuestion) {
            $preparedGeneralExamQuestion->delete();
        }

    }

    /**
     * Handle the true false question "restored" event.
     *
     * @param  \App\TrueFalseQuestion  $trueFalseQuestion
     * @return void
     */
    public function restored(TrueFalseQuestion $trueFalseQuestion)
    {
    }

    /**
     * Handle the true false question "force deleted" event.
     *
     * @param  \App\TrueFalseQuestion  $trueFalseQuestion
     * @return void
     */
    public function forceDeleted(TrueFalseQuestion $trueFalseQuestion)
    {
    }
}
