<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\DragDropUseCase;


use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepository;

use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class FillDragDropUseCaseUseCase implements FillDragDropUseCaseUseCaseInterface
{
    private $dragDropRepository;


    public function __construct(DragDropRepositoryInterface $dragDropRepository)
    {
        $this->dragDropRepository = $dragDropRepository;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user)
    {
        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $questions = $resourceSubjectFormatSubjectData->questions;
        $options = $resourceSubjectFormatSubjectData->options;

        $dragDropData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
            'question_feedback' => $resourceSubjectFormatSubjectData->question_feedback,
            'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            'drag_drop_type' => $resourceSubjectFormatSubjectData->drag_drop_type,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();
        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnDragDrop = $this->dragDropRepository->getDragDropDateBySubjectFormatId($resourceSubjectFormatId);
            if ($returnDragDrop) {
                $dragDrop = $returnDragDrop;
            } else {
                $dragDrop = $this->dragDropRepository->create($dragDropData);
            }

        } else {
            $dragDrop = $this->dragDropRepository->findOrFail($resourceSubjectFormatSubjectDataId);
        }

        $dragDropRepo = new DragDropRepository($dragDrop);

        $dragDropType = getValueFromAcceptCriteria($dragDrop->resourceSubjectFormatSubject->accept_criteria ,'drag_drop_type');

        $dragDropRepo->update($dragDropData);

        //////Options
        $this->deleteOptions($dragDropRepo, $options);

        $optionPairIds = $this->createOrUpdateOptions($dragDropRepo, $dragDrop->id, $options);

        //////Questions
        $this->deleteQuestions($dragDropRepo, $questions);

        $this->createOrUpdateQuestions($dragDropRepo, $dragDrop->id, $questions, $optionPairIds);


    }

    private function createOrUpdateQuestions(DragDropRepository $dragDropRepo, $dragDropId, $questions, $optionPairIds)
    {
        foreach ($questions as $question) {
            $questionId = $question->id;

            $questionAnswers = $optionPairIds[$question->answers] ?? null;


            $questionData = [
                'question' => $question->question ?? null,
                'image' => $question->image ?? null,
                'res_drag_drop_data_id' => $dragDropId,
                'correct_option_id' => $questionAnswers,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $dragDropRepo->createQuestion($questionData);
            } else {
                $questionObj = $dragDropRepo->updateQuestion($questionId, $questionData);
            }
            attachQuestionMeida($question, $questionObj, 'subject/drag_drop');

        }

    }

    private function deleteQuestions(DragDropRepository $dragDropRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $dragDropRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $dragDropRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }

        $dragDropRepo->deleteQuestionsIds($deleteIds);

    }


    private function createOrUpdateOptions(DragDropRepository $dragDropRepo, $dragDropId, $options)
    {
        $optionPairIds = [];
        foreach ($options as $option) {
            $optionId = $option->id;

            $optionData = [
                'option' => $option->option ?? null,
                'res_drag_drop_data_id' => $dragDropId,
            ];
            if (Str::contains($optionId, 'new')) {
                $optionObj = $dragDropRepo->createOption($optionData);
            } else {
                $optionObj = $dragDropRepo->updateOption($optionId, $optionData);
            }
            $optionPairIds[$optionId] = $optionObj->id;
        }
        return $optionPairIds;
    }


    private function deleteOptions(DragDropRepository $dragDropRepo, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldOptionsIds = $dragDropRepo->getOptionsIds();
        $deleteIds = array_diff($oldOptionsIds, $newIds);
        $dragDropRepo->deleteOptionsIds($deleteIds);

    }

}
