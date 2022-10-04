<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\DragDropUseCase;


use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepositoryInterface;

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
    public function fillResource(int $dataId, $data)
    {
        $questions = $data->questions;
        $options = $data->options;

        $dragDrop = $this->dragDropRepository->findOrFail($dataId);

        $dragDropRepo = new DragDropRepository($dragDrop);
        $dragDropRepo->update(['description' => $data->description]);

        //////Options
        $this->deleteOptions($dragDropRepo, $options);

        $optionPairIds = $this->createOrUpdateOptions($dragDropRepo, $dragDrop->id, $options);

        //////Questions
        $this->deleteQuestions($dragDropRepo, $questions);

        $this->createOrUpdateQuestions($dragDropRepo, $dragDrop->id, $questions, $optionPairIds);

        return $this->dragDropRepository->findOrFail($dataId);

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
