<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers\HotSpot;

use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;

class HotSpotQuestionObserver
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
     * @param   $hotpsotQuestion
     * @return void
     */
    public function created(HotSpotQuestion $hotpsotQuestion)
    {
        $this->createUpdatePrepareQuestionUseCase->createPrepareExamQuestion($hotpsotQuestion);
        $this->preperedGeneralExamQuestionUseCase->prepareQuestion($hotpsotQuestion);
    }


    /**
     * Handle the true false question "deleted" event.
     *
     * @param  \App\HotSpotQuestion  $hotpsotQuestion
     * @return void
     */
    public function deleted(HotSpotQuestion $hotpsotQuestion)
    {
        $prepareExamQuestion = $hotpsotQuestion->prepareExamQuestion()->first();
        if ($prepareExamQuestion) {
            $prepareExamQuestion->delete();
        }

        $preparedGeneralExamQuestion = $hotpsotQuestion->preparedGeneralExamQuestion()->first();
        if ($preparedGeneralExamQuestion) {
            $preparedGeneralExamQuestion->delete();
        }

    }
}
