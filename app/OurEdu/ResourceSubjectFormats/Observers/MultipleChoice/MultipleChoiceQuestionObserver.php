<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers\MultipleChoice;

use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;

class MultipleChoiceQuestionObserver
{
    private $createUpdatePrepareQuestionUseCase;
    protected $preperedGeneralExamQuestionUseCase;
    public function __construct(CreateUpdatePrepareQuestionUseCaseInterface $createUpdatePrepareQuestionUseCase, PreperedGeneralExamQuestionUseCaseInterface $preperedGeneralExamQuestionUseCase)
    {
        $this->createUpdatePrepareQuestionUseCase=$createUpdatePrepareQuestionUseCase;
        $this->preperedGeneralExamQuestionUseCase = $preperedGeneralExamQuestionUseCase;
    }

    /**
     * Handle the true false question "created" event.
     *
     * @param   $multipleChoiceQuestion
     * @return void
     */
    public function created(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        if (!$multipleChoiceQuestion->model || $multipleChoiceQuestion->model == QuestionModelsEnums::EXAM) {
            $this->createUpdatePrepareQuestionUseCase->createPrepareExamQuestion($multipleChoiceQuestion);

            $this->preperedGeneralExamQuestionUseCase->prepareQuestion($multipleChoiceQuestion);
        }
    }

    /**
     * Handle the true false question "updated" event.
     *
     * @param  \App\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return void
     */
    public function updated(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
    }

    /**
     * Handle the true false question "deleted" event.
     *
     * @param  \App\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return void
     */
    public function deleted(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $prepareExamQuestion = $multipleChoiceQuestion->prepareExamQuestion()->first();
        if ($prepareExamQuestion) {
            $prepareExamQuestion->delete();
        }

        $preparedGeneralExamQuestion = $multipleChoiceQuestion->preparedGeneralExamQuestion()->first();
        if ($preparedGeneralExamQuestion) {
            $preparedGeneralExamQuestion->delete();
        }

    }

    /**
     * Handle the true false question "restored" event.
     *
     * @param  \App\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return void
     */
    public function restored(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
    }

    /**
     * Handle the true false question "force deleted" event.
     *
     * @param  \App\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return void
     */
    public function forceDeleted(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
    }
}
