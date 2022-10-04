<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\MatchingUseCase;


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
    public function fillResource(int $resourceSubjectFormatId, $data)
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

            $return = $this->matchingRepository->getMatchingDataBySubjectFormatId($resourceSubjectFormatId);
            if ($return) {
                $matching = $this->matchingRepository->findOrFail($return->id);
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

        return [
            'status' => 200,
            'detail' => trans('resourceSubjectFormat.Questions created Successfully'),
            'title' => trans('resourceSubjectFormat.Questions created Successfully'),
        ];
    }

    private function createOrUpdateQuestions(MatchingRepository $matchingRepo, $matchingId, $questions)
    {
        $questionsMap = [];
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'text' => $question->text ?? null,
                'res_matching_data_id' => $matchingId,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $matchingRepo->createQuestion($questionData);
                $questionsMap[$questionId] = $questionObj->id;
            } else {
                $questionObj = $matchingRepo->updateQuestion($questionId, $questionData);
            }
            attachQuestionMeida($question, $questionObj, 'subject/drag_drop');
            attachQuestionVideo($question, $questionObj, 'subject/drag_drop');
            attachQuestionAudio($question, $questionObj, 'subject/drag_drop');
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
                $optionData['res_matching_question_id'] = $questionsMap[$questionId];
            } else {
                $optionData['res_matching_question_id'] = $questionId;
            }

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $matchingRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert = $matchingRepo->insertMultipleOptions($optionsDataMultiple);
        }
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
            if (array_search($option->question_id, $questionsIds) === false) {
                return [
                    'status' => 422,
                    'title' => 'question_id_must_existed',
                    'detail' => trans('option question_id not existed in question list')
                ];
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
