<?php


namespace App\OurEdu\Subjects\UseCases\EditResource\DragDropUseCase;


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
    public function fillResource(int $resourceSubjectFormatId, $data)
    {

        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $questions = $resourceSubjectFormatSubjectData->questions;
        $options = $resourceSubjectFormatSubjectData->options;

        $errors = $this->validateDragDropQuestions($data);

        if (count($errors)) {
            return $errors;
        }

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

        $dragDropType = getValueFromAcceptCriteria($dragDrop->resourceSubjectFormatSubject->accept_criteria, 'drag_drop_type');

        $dragDropRepo->update($dragDropData);

        //////Options
        $this->deleteOptions($dragDropRepo, $options);

        $optionPairIds = $this->createOrUpdateOptions($dragDropRepo, $dragDrop->id, $options);

        //////Questions
        $this->deleteQuestions($dragDropRepo, $questions);

        $this->createOrUpdateQuestions($dragDropRepo, $dragDrop->id, $questions, $optionPairIds);

        return [
            'status' => 200,
            'detail' => trans('resourceSubjectFormat.Questions created Successfully'),
            'title' => trans('resourceSubjectFormat.Questions created Successfully'),
        ];
    }

    private function createOrUpdateQuestions(DragDropRepository $dragDropRepo, $dragDropId, $questions, $optionPairIds)
    {
        foreach ($questions as $question) {
            $questionId = $question->id;

            $questionAnswers = $optionPairIds[$question->answers] ?? null;


            $questionData = [
                'question' => $question->question ?? null,
                'image' => $question->image ?? null,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
                'res_drag_drop_data_id' => $dragDropId,
                'correct_option_id' => $questionAnswers,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $dragDropRepo->createQuestion($questionData);
            } else {
                $questionObj = $dragDropRepo->updateQuestion($questionId, $questionData);
            }
            attachQuestionMeida($question, $questionObj, 'subject/drag_drop');
            attachQuestionVideo($question, $questionObj, 'subject/drag_drop');
            attachQuestionAudio($question, $questionObj, 'subject/drag_drop');
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

    private function validateDragDropQuestions($data)
    {
        $errors = [];
        $dragDropData = $data->resource_subject_format_subject_data;
        $questions = $dragDropData->questions;
        $options = $dragDropData->options;

        if (empty($dragDropData->question_feedback) || $dragDropData->question_feedback == '' || ctype_space($dragDropData->question_feedback)) {
            return [
                'status' => 422,
                'title' => 'question_feedback required',
                'detail' => trans('general_quizzes.is_required', ['field' => trans("general_quizzes.question_feedback")])
            ];
        }

        if (empty($dragDropData->description) || $dragDropData->description == '' || ctype_space($dragDropData->description)) {
            return [
                'status' => 422,
                'title' => 'description required',
                'detail' => trans('general_quizzes.is_required', ['field' => trans("general_quizzes.description")])
            ];
        }

        if (count($questions) <= 0 ) {
            return  [
                "status" => 422,
                'title' => "questions options",
                'detail' => trans('general_quizzes.you have to insert at least one question'),
            ];
        }

        $questionsAnswersKeys = [];
        foreach ($questions as $key => $question) {
            $questionsAnswersKeys[] = $question->answers;

            if (!property_exists($question, 'answers') || $question->answers == '') {
                return [
                    'status' => 422,
                    'title' => 'answer required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans("general_quizzes.answer")])
                ];
            }

            if (!property_exists($question, 'question') || $question->question == '' || ctype_space($question->question)) {
                return [
                    'status' => 422,
                    'title' => 'question question',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans("general_quizzes.question")])
                ];
            }

            $questionData = trim($question->question, '*__*');
            if (strlen(trim($questionData, ' ')) == 0) {
                return [
                    'status' => 422,
                    'title' => 'question text is required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans("general_quizzes.question")])
                ];
            }

            if (strpos($question->question, "*__*") ==false) {
                return [
                    'status' => 422,
                    'title' => 'question question',
                    'detail' => trans('general_quizzes.you have to specify the answer place')
                ];
            }
        }

        $optionsKeys = [];
        foreach ($options as $key => $option) {
            if (!property_exists($option, 'option') || $option->option == '' || ctype_space($option->option)) {
                return [
                    'status' => 422,
                    'title' => 'option required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans("general_quizzes.option")])
                ];
            }
            $optionsKeys[] = $option->id;
        }
        if (count($options) <= 0 || count(array_intersect($optionsKeys, $questionsAnswersKeys)) < count($questionsAnswersKeys)) {
            return  [
                "status" => 422,
                'title' => "questions options",
                'detail' => trans('general_quizzes.you have to insert the answers to Questions'),
            ];
        }
        return [$errors];
    }
}
