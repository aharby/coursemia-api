<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers\DragDrop;

use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;

class DragDropDataObserver
{
    private $createUpdatePrepareQuestionUseCase;
    protected $prepareGeneralExamQuestionUseCase;
    public function __construct(CreateUpdatePrepareQuestionUseCaseInterface $createUpdatePrepareQuestionUseCase, PreperedGeneralExamQuestionUseCaseInterface $prepareGeneralExamQuestionUseCase)
    {
        $this->createUpdatePrepareQuestionUseCase=$createUpdatePrepareQuestionUseCase;
        $this->prepareGeneralExamQuestionUseCase = $prepareGeneralExamQuestionUseCase;
    }

    /**
     * Handle the true false question "created" event.
     *
     * @param   $dragDropData
     * @return void
     */
    public function created(DragDropData $dragDropData)
    {
        if (!$dragDropData->model || $dragDropData->model == QuestionModelsEnums::EXAM) {
            $this->createUpdatePrepareQuestionUseCase->createPrepareExamQuestion($dragDropData);
            $this->prepareGeneralExamQuestionUseCase->prepareQuestion($dragDropData);
        }
    }

    /**
     * Handle the true false question "updated" event.
     *
     * @param  \App\DragDropData  $dragDropData
     * @return void
     */
    public function updated(DragDropData $dragDropData)
    {
    }

    /**
     * Handle the true false question "deleted" event.
     *
     * @param  \App\DragDropData  $dragDropData
     * @return void
     */
    public function deleted(DragDropData $dragDropData)
    {
    }

    /**
     * Handle the true false question "restored" event.
     *
     * @param  \App\DragDropData  $dragDropData
     * @return void
     */
    public function restored(DragDropData $dragDropData)
    {
    }

    /**
     * Handle the true false question "force deleted" event.
     *
     * @param  \App\DragDropData  $dragDropData
     * @return void
     */
    public function forceDeleted(DragDropData $dragDropData)
    {
    }
}
