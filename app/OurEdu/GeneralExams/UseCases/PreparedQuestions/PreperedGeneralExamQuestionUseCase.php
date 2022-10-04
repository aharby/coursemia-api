<?php

namespace App\OurEdu\GeneralExams\UseCases\PreparedQuestions;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use App\OurEdu\GeneralExams\Repository\PreparedQuestion\PreparedGeneralExamQuestionRepositoryInterface;

class PreperedGeneralExamQuestionUseCase implements PreperedGeneralExamQuestionUseCaseInterface
{
    protected $preparedQuestionRepository;

    public function __construct(PreparedGeneralExamQuestionRepositoryInterface $preparedQuestionRepository)
    {
        $this->preparedQuestionRepository = $preparedQuestionRepository;
    }

    public function prepareQuestion($model)
    {
        $class = get_class($model);

        $data = [];
        switch ($class) {
            case TrueFalseQuestion::class:
                $data = $this->questionHandle($model);
                break;
            case MultipleChoiceQuestion::class:
                $data = $this->questionHandle($model);
                break;
            case MatchingData::class:
                $data = $this->dataHandle($model);
                break;
            case MultiMatchingData::class:
                $data = $this->dataHandle($model);
                break;
            case DragDropData::class:
                $data = $this->dataHandle($model);
                break;
            case CompleteQuestion::class:
                $data = $this->questionHandle($model);
                break;
            case HotSpotQuestion::class:
                $data = $this->questionHandle($model);
                break;
        }

       $this->preparedQuestionRepository->create($data);
    }

    protected function questionHandle($model)
    {
        $resourceSubjectFormatSubject = $model->parentData->resourceSubjectFormatSubject;

        $difficultyLevel = json_decode($resourceSubjectFormatSubject->accept_criteria)->difficulty_level ?? '';

        return [
            'question_type' => $resourceSubjectFormatSubject->resource_slug,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id ?? null,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'questionable_id' => $model->id,
            'questionable_type' => get_class($model),
            'difficulty_level_id' => $difficultyLevel
        ];
    }

    protected function dataHandle($model)
    {
        $resourceSubjectFormatSubject = $model->resourceSubjectFormatSubject;

        $difficultyLevel = json_decode($resourceSubjectFormatSubject->accept_criteria)->difficulty_level ?? '';

        return [
            'question_type' => $resourceSubjectFormatSubject->resource_slug,
            'subject_id' => $resourceSubjectFormatSubject->subjectFormatSubject->subject_id ?? null,
            'subject_format_subject_id' => $resourceSubjectFormatSubject->subject_format_subject_id,
            'questionable_id' => $model->id,
            'questionable_type' => get_class($model),
            'difficulty_level_id' => $difficultyLevel
        ];
    }
}
