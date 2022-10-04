<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\HotSpotUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\HotSpot\HotSpotRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\HotSpot\HotSpotRepositoryInterface;

use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillHotSpotUseCase implements FillHotSpotUseCaseInterface
{
    private $hotSpotRepository;


    public function __construct(HotSpotRepositoryInterface $hotSpotRepository)
    {
        $this->hotSpotRepository = $hotSpotRepository;
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
        $hotSpotData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'list_order_key' => $resourceSubjectFormatSubjectData->list_order_key,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $hotSpot = $this->hotSpotRepository->create($hotSpotData);
        } else {
            $hotSpot = $this->hotSpotRepository->findOrFail($resourceSubjectFormatSubjectDataId);
        }

        $hotSpotRepo = new HotSpotRepository($hotSpot);
        $hotSpotRepo->update([
            'description' => $resourceSubjectFormatSubjectData->description,
            'list_order_key' => $resourceSubjectFormatSubjectData->list_order_key,
        ]);


        $this->deleteQuestions($hotSpotRepo, $questions);


        $this->createOrUpdateQuestions($hotSpotRepo, $hotSpot->id, $questions);

        return $hotSpot->fresh();
    }

    private function deleteQuestions(HotSpotRepositoryInterface $hotSpotRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $hotSpotRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $hotSpotRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }


        $hotSpotRepo->deleteQuestionsIds($deleteIds);
    }

    private function createOrUpdateQuestions(HotSpotRepositoryInterface $hotSpotRepo, $hotSpotId, $questions)
    {
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'question' => $question->question,
                'res_hot_spot_data_id' => $hotSpotId,
                'question_feedback' => $question->question_feedback ?? null,
                'image_width' => $question->image_width ?? null,
            ];
            $answers = $question->answers;
            if (Str::contains($questionId, 'new')) {
                $questionObj = $hotSpotRepo->createQuestion($questionData);
            } else {
                $questionObj = $hotSpotRepo->updateQuestion($questionId, $questionData);
            }
            $this->deleteAnswers($hotSpotRepo, $answers, $questionObj->id);

            $this->createOrUpdateAnswers($hotSpotRepo, $answers, $questionObj->id);
            attachQuestionMeida($question, $questionObj, 'subject/hot_spot');
        }
    }

    private function deleteAnswers(HotSpotRepositoryInterface $hotSpotRepo, $answers, $questionId)
    {
        $newIds = Arr::pluck($answers, 'id');
        $oldAnswersIds = $hotSpotRepo->getAnswersIds($questionId);

        $deleteIds = array_diff($oldAnswersIds, $newIds);
        $hotSpotRepo->deleteAnswersIds($deleteIds);
    }

    private function createOrUpdateAnswers(HotSpotRepositoryInterface $hotSpotRepo, $answers, $questionId)
    {
        //save answer of hotspot media question as json representation of x and y coordinates on polygon shape.
        foreach ($answers as $answer) {
            $answerId = $answer->id;
            $answerData = [
                'answer' => json_encode($answer->answer),
                'res_hot_spot_question_id' => $questionId,
            ];

            if (Str::contains($answerId, 'new')) {
                $hotSpotRepo->insertAnswer($answerData);
            } else {
                $hotSpotRepo->updateAnswer($answerId, $answerData);
                $hotSpotRepo->findAnswer($answerId);
            }
        }
    }
}
