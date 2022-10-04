<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers\Matching;

use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;

class MatchingDataObserver
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
    public function created(MatchingData $matchingData)
    {
        $this->createUpdatePrepareQuestionUseCase->createPrepareExamQuestion($matchingData);
        $this->prepareGeneralExamQuestionUseCase->prepareQuestion($matchingData);
    }

    /**
     * Handle the true false question "updated" event.
     *
     * @param  \App\MatchingData  $matchingData
     * @return void
     */
    public function updated(MatchingData $matchingData)
    {
    }

    /**
     * Handle the true false question "deleted" event.
     *
     * @param  \App\MatchingData  $matchingData
     * @return void
     */
    public function deleted(MatchingData $matchingData)
    {
    }

    /**
     * Handle the true false question "restored" event.
     *
     * @param  \App\MatchingData  $matchingData
     * @return void
     */
    public function restored(MatchingData $matchingData)
    {
    }

    /**
     * Handle the true false question "force deleted" event.
     *
     * @param  \App\MatchingData  $matchingData
     * @return void
     */
    public function forceDeleted(MatchingData $matchingData)
    {
    }
}
