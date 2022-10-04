<?php

namespace App\OurEdu\Subjects\UseCases\EditResource\MultipleChoiceUseCase;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillMultipleChoiceUseCase implements FillMultipleChoiceUseCaseInterface
{
    private $multipleChoiceRepository;
    private $resourceSubjectFormatSubjectRep;


    public function __construct(MultipleChoiceRepositoryInterface $multipleChoiceRepository,ResourceSubjectFormatSubjectRepository $resourceSubjectFormatSubjectRepository)
    {
        $this->multipleChoiceRepository = $multipleChoiceRepository;
        $this->resourceSubjectFormatSubjectRep = $resourceSubjectFormatSubjectRepository;
    }


    public function fillResource(int $resourceSubjectFormatId, $data)
    {

        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $acceptanceCriteria = json_decode($this->resourceSubjectFormatSubjectRep->findOrFail($resourceSubjectFormatId)->accept_criteria,true);
        $multipleChoiceTypeId = $acceptanceCriteria['multiple_choice_type'];
        $questions = $resourceSubjectFormatSubjectData->questions;
        $multipleChoiceData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
            'multiple_choice_type' => $multipleChoiceTypeId,
        ];

        $validationErrors = $this->validateQuestions($questions);

        if (count($validationErrors)) {
            return $validationErrors;
        }

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $multipleChoice = $this->multipleChoiceRepository->getMultipleChoiceDataBySubjectFormatId($resourceSubjectFormatId);
            if (!$multipleChoice) {
                $multipleChoice = $this->multipleChoiceRepository->create($multipleChoiceData);
            }
        } else {
            $multipleChoice = $this->multipleChoiceRepository->findOrFail($resourceSubjectFormatSubjectDataId);
        }
        $multipleChoiceRepo = new MultipleChoiceRepository($multipleChoice);

        $multipleChoiceRepo->update(['description'=>$resourceSubjectFormatSubjectData->description]);

        $this->deleteQuestions($multipleChoiceRepo, $questions);

        $this->createOrUpdateQuestions($multipleChoiceRepo, $multipleChoice->id, $questions);

        return [
            'status' => 200,
            'detail' => trans('resourceSubjectFormat.Questions created Successfully'),
            'title' => trans('resourceSubjectFormat.Questions created Successfully'),
        ];
    }

    private function deleteQuestions(MultipleChoiceRepository $multipleChoiceRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $multipleChoiceRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $multipleChoiceRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }


        $multipleChoiceRepo->deleteQuestionsIds($deleteIds);

    }

    private function createOrUpdateQuestions(
        MultipleChoiceRepository $multipleChoiceRepo,
        $multipleChoiceId,
        $questions
    ) {
        foreach ($questions as $question) {
            $questionId = $question->id;

            $questionData = [
                'question' => $question->question ?? null,
                'url' => $question->url ?? '',
                'question_feedback' => $question->question_feedback ?? null,
                'res_multiple_choice_data_id' => $multipleChoiceId,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $multipleChoiceRepo->createQuestion($questionData);
            } else {
                $questionObj = $multipleChoiceRepo->updateQuestion($questionId, $questionData);
            }
            $this->createOrUpdateOptions($multipleChoiceRepo, $questionObj->id, $question);

            attachQuestionMeida($question, $questionObj, 'subject/multiple_choice');
            attachQuestionVideo($question, $questionObj, 'subject/multiple_choice');
            attachQuestionAudio($question, $questionObj, 'subject/multiple_choice');
        }
    }

    private function createOrUpdateOptions(MultipleChoiceRepository $multipleChoiceRepo, $questionId, $question)
    {
        $options = $question->options ?? [];

        $optionsDataMultiple = [];
        $this->deleteOptions($multipleChoiceRepo, $questionId, $options);

        foreach ($options as $option) {
            $optionId = $option->id;
            $optionData = [
                'answer' => $option->option,
                'is_correct_answer' => $option->is_correct_answer,
                'res_multiple_choice_question_id' => $questionId,
            ];

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $multipleChoiceRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $multipleChoiceRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions(MultipleChoiceRepository $multipleChoiceRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $multipleChoiceRepo->getQuestionOptionsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $multipleChoiceRepo->deleteOptions($questionId, $deleteIds);
    }

    public function validateQuestions($questions): array
    {
        $validationResult = [];

        foreach ($questions as $question) {
            if (!strlen($question->question)) {
                return [
                    'status' => '422',
                    'title' => trans('resourceSubjectFormat.question is required'),
                    'detail' => trans('resourceSubjectFormat.question is required'),
                ];
            }

            $options = $question->options ?? [];

            if (count($options) < 2) {
                return [
                    'status' => '422',
                    'title' => trans('resourceSubjectFormat.the question must have at least 2 options'),
                    'detail' => trans('resourceSubjectFormat.the question must have at least 2 options'),
                ];
            }
            $countCorrectAnswers = 0;

            foreach ($options as $option) {
                if (!strlen($option->option)) {
                    return [
                        'status' => '422',
                        'title' => trans('resourceSubjectFormat.option of question is required'),
                        'detail' => trans('resourceSubjectFormat.option of question is required'),
                    ];
                }

                if ($option->is_correct_answer) {
                    $countCorrectAnswers++;
                }
            }

            if ($countCorrectAnswers < 1) {
                return [
                    'status' => '422',
                    'title' => trans('resourceSubjectFormat.you must select one correct answers at least for each question'),
                    'detail' => trans('resourceSubjectFormat.you must select one correct answers at least for each question'),
                ];
            }
        }

        return $validationResult;
    }
}
