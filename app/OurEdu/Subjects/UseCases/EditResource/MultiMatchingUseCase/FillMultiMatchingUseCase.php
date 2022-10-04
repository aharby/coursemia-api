<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\MultiMatchingUseCase;


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
     * @return array
     */
    public function fillResource(int $resourceSubjectFormatId, $data): array
    {

        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $questions = $resourceSubjectFormatSubjectData->questions;
        $options = $resourceSubjectFormatSubjectData->options;

        $errors = $this->validateMatchingQuestions($questions, $options);

        if (count($errors)) {
            return $errors;
        }

        $matchingData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'question_feedback' => $resourceSubjectFormatSubjectData->question_feedback,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
            'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {

            $multiMatching = $this->multiMatchingRepository->getMultiMatchingDataBySubjectFormatId($resourceSubjectFormatId);
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

        return [
            'status' => 200,
            'detail' => trans('resourceSubjectFormat.Questions created Successfully'),
            'title' => trans('resourceSubjectFormat.Questions created Successfully'),
        ];
    }

    private function createOrUpdateQuestions(MultiMatchingRepository $multiMatchingRepo, $matchingId, $questions)
    {
        $questionMap = [];
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'text' => $question->text ?? null,
                'res_multi_matching_data_id' => $matchingId,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $multiMatchingRepo->createQuestion($questionData);
                $questionMap[$questionId] = $questionObj->id;
            } else {
                $questionObj = $multiMatchingRepo->updateQuestion($questionId, $questionData);
            }
            attachQuestionMeida($question, $questionObj, 'subject/multi_matching');
            attachQuestionVideo($question, $questionObj, 'subject/multi_matching');
            attachQuestionAudio($question, $questionObj, 'subject/multi_matching');
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

    private function validateMatchingQuestions($questions, $options): array
    {
        $questionsIds = [];

        foreach ($questions as $question) {
            $questionsIds[] = $question->id;

            if (!property_exists($question, 'text') || $question->text == '' || ctype_space($question->text)) {
                return [
                    'status' => 422,
                    'title' => 'text_is_missing',
                    'detail' => trans('question text is required')
                ];
            }
        }

        foreach ($options as $option) {
            foreach ($option->questions as $questionId) {
                if (array_search($questionId, $questionsIds) === false) {
                    return [
                        'status' => 422,
                        'title' => 'question_id_must_existed',
                        'detail' => trans('option question_id not existed in question list')
                    ];
                }
            }

            if (!property_exists($option, 'option') || $option->option == '' || ctype_space($option->option)) {
                return [
                    'status' => 422,
                    'title' => 'option_is_missing',
                    'detail' => trans('resourceSubjectFormat.option of question is required')
                ];
            }
        }

        return [];
    }

}
