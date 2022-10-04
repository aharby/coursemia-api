<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\TrueFalseUseCase;


use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepositoryInterface;

use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class FillTrueFalseUseCase implements FillTrueFalseUseCaseInterface
{
    private $trueFalseRepository;
    private $resourceSubjectFormatSubjectRepo;


    public function __construct(
        TrueFalseRepositoryInterface $trueFalseRepository,
        ResourceSubjectFormatSubjectRepositoryInterface $resourceSubjectFormatSubjectRepository
    )
    {
        $this->trueFalseRepository = $trueFalseRepository;
        $this->resourceSubjectFormatSubjectRepo = $resourceSubjectFormatSubjectRepository;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */
    public function fillResource(int $resourceSubjectFormatId, $data): array
    {
        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $acceptanceCriteria = json_decode($this->resourceSubjectFormatSubjectRepo->findOrFail($resourceSubjectFormatId)->accept_criteria, true);
        $trueFalseTypeId = $acceptanceCriteria['true_false_type'];
        $questions = $resourceSubjectFormatSubjectData->questions;
        $trueFalseType = $resourceSubjectFormatSubjectData->true_false_type;

        $errors = $this->validateTrueFalseQuestions($questions, $trueFalseType);
        if (count($errors)) {
            return $errors;
        }

        $trueFalseData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
            'true_false_type' => $trueFalseTypeId,
            'question_type' => $resourceSubjectFormatSubjectData->question_type,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $trueFalse = $this->trueFalseRepository->create($trueFalseData);
        } else {
            $trueFalse = $this->trueFalseRepository->findOrFail($resourceSubjectFormatSubjectDataId);
        }
        $trueFalseRepo = new TrueFalseRepository($trueFalse);

        $trueFalseRepo->update(['description' => $resourceSubjectFormatSubjectData->description]);

        $this->deleteQuestions($trueFalseRepo, $questions);

        $this->createOrUpdateQuestions($trueFalseRepo, $trueFalse->id, $questions);

        return [
            'status' => 200,
            'detail' => trans('resourceSubjectFormat.Questions created Successfully'),
            'title' => trans('resourceSubjectFormat.Questions created Successfully'),
        ];
    }

    private function createOrUpdateQuestions(TrueFalseRepository $trueFalseRepo, $trueFalseId, $questions)
    {
        foreach ($questions as $question) {
            $questionId = $question->id;

            $questionData = [
                'text' => $question->text ?? null,
                'image' => $question->image ?? null,
                'question_feedback' => $question->question_feedback ?? null,
                'res_true_false_data_id' => $trueFalseId,
                'is_true' => (int)$question->is_true,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $trueFalseRepo->createQuestion($questionData);
            } else {
                $questionObj = $trueFalseRepo->updateQuestion($questionId, $questionData);
            }
            $this->createOrUpdateOptions($trueFalseRepo, $questionObj->id, $question);
            attachQuestionMeida($question, $questionObj, 'subject/true_false');
            attachQuestionVideo($question, $questionObj, 'subject/true_false');
            attachQuestionAudio($question, $questionObj, 'subject/true_false');
        }

    }

    private function deleteQuestions(TrueFalseRepository $trueFalseRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $trueFalseRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $trueFalseRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }


        $trueFalseRepo->deleteQuestionsIds($deleteIds);

    }


    private function createOrUpdateOptions(TrueFalseRepository $trueFalseRepo, $questionId, $question)
    {
        $options = $question->options ?? [];

        $optionsDataMultiple = [];
        $this->deleteOptions($trueFalseRepo, $questionId, $options);

        foreach ($options as $option) {

            $optionId = $option->id;
            $optionData = [
                'option' => $option->option,
                'is_correct_answer' => $option->is_correct,
                'res_true_false_question_id' => $questionId,
            ];

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $trueFalseRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
           $insert= $trueFalseRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions(TrueFalseRepository $trueFalseRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $trueFalseRepo->getQuestionOptionsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $trueFalseRepo->deleteOptions($questionId, $deleteIds);
    }

    private function validateTrueFalseQuestions($questions, string $type = null): array
    {
        foreach ($questions as $key => $question) {
            if (!$question->text ||  $question->text == '' || $question->text== ' ') {
                return [
                    'status' => 422,
                    'title' => 'question_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans('general_quizzes.question')])
                ];
            }
            if (!$question->question_feedback ||  $question->question_feedback == '' || $question->question_feedback== ' ') {
                return [
                    'status' => 422,
                    'title' => 'question_feedback_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field'=>trans('general_quizzes.question_feedback')])
                ];
            }

            if (!is_null($type) and $type == ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT) {
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

                    if ($option->is_correct) {
                        $countCorrectAnswers++;
                    }
                }

                if ($countCorrectAnswers < 2) {
                    return [
                        'status' => '422',
                        'title' => trans('resourceSubjectFormat.you must select one correct answers at least for each question'),
                        'detail' => trans('resourceSubjectFormat.you must select one correct answers at least for each question'),
                    ];
                }
            }
        }

        return [];
    }
}
