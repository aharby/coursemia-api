<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MatchingUseCase;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Repository\Matching\MatchingRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Matching\MatchingRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillMatchingUseCase implements FillMatchingUseCaseInterface
{
    private $matchingRepository;


    public function __construct(MatchingRepositoryInterface $matchingRepository)
    {
        $this->matchingRepository = $matchingRepository;
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
        $matchingData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'question_feedback' => $resourceSubjectFormatSubjectData->question_feedback,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
            'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {

            $matching = $this->matchingRepository->getMatchingDataBySubjectFormatId($resourceSubjectFormatId);
            if ($matching) {
                $matchingRepo = new MatchingRepository($matching);

                $matchingRepo->update($matchingData);
            } else {
                $matching = $this->matchingRepository->create($matchingData);
            }
        } else {
            $matching = $this->matchingRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $matchingRepo = new MatchingRepository($matching);

            $matchingRepo->update($matchingData);
        }

        $matchingRepo = new MatchingRepository($matching);

        $this->deleteQuestions($matchingRepo, $questions);
        $this->deleteOptions($matchingRepo, $options);


        //Should Return Array Of mapping between new_id and real database ids
        $questionsMap = $this->createOrUpdateQuestions($matchingRepo, $matching->id, $questions);

        $this->createOrUpdateOptions($matchingRepo, $matching->id, $options, $questionsMap);
    }

    private function createOrUpdateQuestions(MatchingRepository $matchingRepo, $matchingId, $questions)
    {
        $questionsMap = [];
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'text' => $question->text ?? null,
                'res_matching_data_id' => $matchingId,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $matchingRepo->createQuestion($questionData);
                $questionsMap[$questionId] = $questionObj->id;
            } else {
                $questionObj = $matchingRepo->updateQuestion($questionId, $questionData);
            }

            attachQuestionMeida($question, $questionObj, 'subject/matching');
        }
        return $questionsMap;
    }

    private function deleteQuestions(MatchingRepository $matchingRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $matchingRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $matchingRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }

        $matchingRepo->deleteQuestionsIds($deleteIds);
    }

    private function deleteOptions(MatchingRepository $matchingRepo, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldOptionsIds = $matchingRepo->getQuestionsIds();

        $deleteIds = array_diff($oldOptionsIds, $newIds);
        $matchingRepo->deleteOptionsIds($deleteIds);
    }

    private function createOrUpdateOptions(MatchingRepository $matchingRepo, $matchingId, $options, $questionsMap)
    {
        $optionsDataMultiple = [];
        foreach ($options as $option) {
            $optionId = $option->id;
            $questionId = $option->question_id;
            $optionData = [
                'option' => $option->option,
                'res_matching_question_id' => $questionId,
                'res_matching_data_id' => $matchingId,
            ];

            if (Str::contains($questionId, 'new')) {
                $optionData['res_matching_question_id'] =  $questionsMap[$questionId];
            } else {
                $optionData['res_matching_question_id'] =  $questionId;
            }

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $matchingRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert= $matchingRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }
}
