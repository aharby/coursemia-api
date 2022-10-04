<?php

namespace App\OurEdu\Exams\UseCases\PrepareExamQuestion\CreateUpdatePrepareQuestionUseCase;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use App\OurEdu\Exams\Repository\PrepareExamQuestion\PrepareExamQuestionRepositoryInterface;

class CreateUpdatePrepareQuestionUseCase implements CreateUpdatePrepareQuestionUseCaseInterface
{
    private $prepareExamQuestionRepository;

    public function __construct(PrepareExamQuestionRepositoryInterface $prepareExamQuestionRepository)
    {
        $this->prepareExamQuestionRepository = $prepareExamQuestionRepository;
    }

    public function createPrepareExamQuestion($model)
    {
        $class = get_class($model);

        $data = [];
        switch ($class) {
            case TrueFalseQuestion::class:
                $data = $this->trueFalseHandel($model);
                break;
            case MultipleChoiceQuestion::class:
                $data = $this->multipleChoiceQuestionHandel($model);
                break;
            case MatchingData::class:
                $data = $this->matchingHandel($model);
                break;
            case MultiMatchingData::class:
                $data = $this->multiMatchingHandel($model);
                break;
            case DragDropData::class:
                $data = $this->dragDropHandel($model);
                break;
            case CompleteQuestion::class:
                $data = $this->completeHandle($model);
                break;

            case HotSpotQuestion::class:
                $data = $this->hotSpotHandle($model);
                break;
        }


        $createData = [
            'question_type' => $data['question_type'],
            'subject_id' => $data['subject_id'],
            'subject_format_subject_id' => $data['subject_format_subject_id'],
            'question_table_id' => $data['table_id'],
            'question_table_type' => $data['table_type'],
            'time_to_solve' => $data['time_to_solve'],
            'difficulty_level' => $data['difficulty_level'],

        ];

        $this->prepareExamQuestionRepository->create($createData);
    }


    private function trueFalseHandel(TrueFalseQuestion $model)
    {
        $resourceSubjectFormatSubject = $model->parentData->resourceSubjectFormatSubject;
        $difficultyLevel = getValueFromAcceptCriteria($resourceSubjectFormatSubject->accept_criteria, 'difficulty_level', true)['slug']??'';

        return [
            'question_type' => LearningResourcesEnums::TRUE_FALSE,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'time_to_solve' => $model->time_to_solve,
            'table_id' => $model->id,
            'table_type' => TrueFalseQuestion::class,
            'difficulty_level' => $difficultyLevel


        ];
    }

    private function multipleChoiceQuestionHandel(MultipleChoiceQuestion $model)
    {
        $resourceSubjectFormatSubject = $model->parentData->resourceSubjectFormatSubject;
        $difficultyLevel = getValueFromAcceptCriteria($resourceSubjectFormatSubject->accept_criteria, 'difficulty_level', true)['slug']??'';

        return [
            'question_type' => LearningResourcesEnums::MULTI_CHOICE,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'data' => $resourceSubjectFormatSubject,
            'time_to_solve' => $model->time_to_solve,
            'table_id' => $model->id,
            'table_type' => MultipleChoiceQuestion::class,
            'difficulty_level' => $difficultyLevel

        ];
    }

    private function matchingHandel(MatchingData $model)
    {
        $resourceSubjectFormatSubject = $model->resourceSubjectFormatSubject;
        $difficultyLevel = getValueFromAcceptCriteria($resourceSubjectFormatSubject->accept_criteria, 'difficulty_level', true)['slug']??'';

        return [
            'question_type' => LearningResourcesEnums::MATCHING,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'time_to_solve' => $model->time_to_solve,
            'table_id' => $model->id,
            'table_type' => MatchingData::class,
            'difficulty_level' => $difficultyLevel

        ];
    }

    private function multiMatchingHandel(MultiMatchingData $model)
    {
        $resourceSubjectFormatSubject = $model->resourceSubjectFormatSubject;
        $difficultyLevel = getValueFromAcceptCriteria($resourceSubjectFormatSubject->accept_criteria, 'difficulty_level', true)['slug']??'';

        return [
            'question_type' => LearningResourcesEnums::MULTIPLE_MATCHING,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'time_to_solve' => $model->time_to_solve,
            'table_id' => $model->id,
            'table_type' => MultiMatchingData::class,
            'difficulty_level' => $difficultyLevel

        ];
    }

    private function dragDropHandel(DragDropData $model)
    {
        $resourceSubjectFormatSubject = $model->resourceSubjectFormatSubject;

        $difficultyLevel = getValueFromAcceptCriteria($resourceSubjectFormatSubject->accept_criteria, 'difficulty_level', true)['slug']??'';

        return [
            'question_type' => LearningResourcesEnums::DRAG_DROP,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'time_to_solve' => $model->time_to_solve,

            'table_id' => $model->id,
            'table_type' => DragDropData::class,
            'difficulty_level' => $difficultyLevel
        ];
    }

    protected function completeHandle(CompleteQuestion $model)
    {
        $resourceSubjectFormatSubject = $model->parentData->resourceSubjectFormatSubject;
        $difficultyLevel = getValueFromAcceptCriteria($resourceSubjectFormatSubject->accept_criteria, 'difficulty_level', true)['slug'] ?? '';

        return [
            'question_type' => LearningResourcesEnums::COMPLETE,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'time_to_solve' => $model->time_to_solve,
            'table_id' => $model->id,
            'table_type' => CompleteQuestion::class,
            'difficulty_level' => $difficultyLevel
        ];
    }

    protected function hotSpotHandle(HotSpotQuestion $model)
    {
        $resourceSubjectFormatSubject = $model->parentData->resourceSubjectFormatSubject;
        $difficultyLevel = getValueFromAcceptCriteria($resourceSubjectFormatSubject->accept_criteria, 'difficulty_level', true)['slug'] ?? '';

        return [
            'question_type' => LearningResourcesEnums::HOTSPOT,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'time_to_solve' => $model->time_to_solve,
            'table_id' => $model->id,
            'table_type' => HotSpotQuestion::class,
            'difficulty_level' => $difficultyLevel
        ];
    }
}
