<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers\Complete;

use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\GeneralExams\UseCases\PreparedQuestions\PreperedGeneralExamQuestionUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase\CreateUpdatePrepareQuestionUseCaseInterface;

class CompleteQuestionObserver
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
     * @param   $completeQuestion
     * @return void
     */
    public function created(CompleteQuestion $completeQuestion)
    {
        if($completeQuestion->model == \App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums::EXAM){
            $this->createUpdatePrepareQuestionUseCase->createPrepareExamQuestion($completeQuestion);
            $this->preperedGeneralExamQuestionUseCase->prepareQuestion($completeQuestion);
        }
    }


     /**
     * Handle the true false question "deleted" event.
     *
     * @param  \App\CompleteQuestion  $completeQuestion
     * @return void
     */
    public function deleted(CompleteQuestion $completeQuestion)
    {
        $prepareExamQuestion = $completeQuestion->prepareExamQuestion()->first();
        if ($prepareExamQuestion) {
            $prepareExamQuestion->delete();
        }

        $preparedGeneralExamQuestion = $completeQuestion->preparedGeneralExamQuestion()->first();
        if ($preparedGeneralExamQuestion) {
            $preparedGeneralExamQuestion->delete();
        }

    }
}
