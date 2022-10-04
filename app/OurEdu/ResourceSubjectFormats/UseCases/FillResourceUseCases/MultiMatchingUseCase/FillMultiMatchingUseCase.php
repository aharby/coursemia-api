<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultiMatchingUseCase;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching\MultiMatchingRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching\MultiMatchingRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillMultiMatchingUseCase implements FillMultiMatchingUseCaseInterface
{
    private $multiMatchingRepository;


    public function __construct(MultiMatchingRepositoryInterface $multiMatchingRepository)
    {
        $this->multiMatchingRepository = $multiMatchingRepository;
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



            $multiMatching = $this->multiMatchingRepository->getMatchingDataBySubjectFormatId($resourceSubjectFormatId);
            if ($multiMatching) {
                $matchingRepo = new MultiMatchingRepository($multiMatching);

                $matchingRepo->update($matchingData);
            } else {
                $multiMatching = $this->multiMatchingRepository->create($matchingData);
            }

        } else {
            $multiMatching = $this->multiMatchingRepository->findOrFail($resourceSubjectFormatSubjectDataId);

            $multiMatchingRepo = new MultiMatchingRepository($multiMatching);

            $multiMatchingRepo->update($matchingData);
        }

        $multiMatchingRepo = new MultiMatchingRepository($multiMatching);

        $this->deleteQuestions($multiMatchingRepo, $questions);
        $this->deleteOptions($multiMatchingRepo, $options);


        //Should Return Array Of mapping between new_id and real database ids
        $questionMap = $this->createOrUpdateQuestions($multiMatchingRepo, $multiMatching->id, $questions);

        $this->createOrUpdateOptions($multiMatchingRepo, $multiMatching->id, $options, $questionMap);
    }

    private function createOrUpdateQuestions(MultiMatchingRepository $multiMatchingRepo, $matchingId, $questions)
    {
        $questionMap = [];
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'text' => $question->text ?? null,
                'res_multi_matching_data_id' => $matchingId,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $multiMatchingRepo->createQuestion($questionData);
                $questionMap[$questionId] = $questionObj->id;
            } else {
                $questionObj = $multiMatchingRepo->updateQuestion($questionId, $questionData);
            }

            attachQuestionMeida($question, $questionObj, 'subject/multi_matching');
        }
        return $questionMap;
    }

    private function deleteQuestions(MultiMatchingRepository $multiMatchingRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $multiMatchingRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $multiMatchingRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }

        $multiMatchingRepo->deleteQuestionsIds($deleteIds);
    }

    private function deleteOptions(MultiMatchingRepository $multiMatchingRepo, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldOptionsIds = $multiMatchingRepo->getQuestionsIds();

        $deleteIds = array_diff($oldOptionsIds, $newIds);
        $multiMatchingRepo->deleteOptionsIds($deleteIds);
    }

    private function createOrUpdateOptions(MultiMatchingRepository $multiMatchingRepo, $matchingId, $options, $questionMap)
    {
//        $optionsDataMultiple = [];
        foreach ($options as $option) {
            $questions = $this->replaceNewWithIds($option->questions, $questionMap);

            $optionId = $option->id;
            $optionData = [
                'option' => $option->option,
                'res_multi_matching_data_id' => $matchingId,
            ];

            if (Str::contains($optionId, 'new')) {
                $option = $multiMatchingRepo->insertOption($optionData);
            } else {
                $multiMatchingRepo->updateOption($optionId, $optionData);
                $option = $multiMatchingRepo->findOption($optionId);
            }

            $option->questions()->sync($questions);
        }
    }

    public function replaceNewWithIds($questions, $questionMap)
    {
        foreach ($questions as $key => $question) {
            if (array_key_exists($question, $questionMap)) {
                $questions[$key] = $questionMap[$question];
            }
        }
        return $questions;
    }
}
