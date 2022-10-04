<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers\MultiMatching;

use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;

class MultiMatchingDataObserver
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
     * @param   $matchingData
     * @return void
     */
    public function created(MultiMatchingData $matchingData)
    {
        $this->createUpdatePrepareQuestionUseCase->createPrepareExamQuestion($matchingData);
        $this->prepareGeneralExamQuestionUseCase->prepareQuestion($matchingData);
    }

    /**
     * Handle the true false question "updated" event.
     *
     * @param MultiMatchingData $multiMatchingData
     * @return void
     */
    public function updated(MultiMatchingData $multiMatchingData)
    {
    }

    /**
     * Handle the true false question "deleted" event.
     *
     * @param  \App\MultiMatchingData  $multiMatchingData
     * @return void
     */
    public function deleted(MultiMatchingData $multiMatchingData)
    {
    }

    /**
     * Handle the true false question "restored" event.
     *
     * @param  \App\MultiMatchingData  $multiMatchingData
     * @return void
     */
    public function restored(MultiMatchingData $multiMatchingData)
    {
    }

    /**
     * Handle the true false question "force deleted" event.
     *
     * @param  \App\MultiMatchingData  $multiMatchingData
     * @return void
     */
    public function forceDeleted(MultiMatchingData $multiMatchingData)
    {
    }
}
