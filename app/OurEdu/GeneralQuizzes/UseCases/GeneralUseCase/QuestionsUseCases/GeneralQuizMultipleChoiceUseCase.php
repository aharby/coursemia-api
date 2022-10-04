<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\Options\Option;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GeneralQuizMultipleChoiceUseCase implements GeneralQuizMultipleChoiceUseCaseInterface
{
    /**
     * @var MultipleChoiceRepositoryInterface
     */
    private $multipleChoiceRepository;
    /**
     * @var QuestionBankRepositoryInterface
     */
    private $questionBankRepository;

    private $multipleChoiceType;
    private $addQuestionBankToGeneralQuiz;

    /**
     * GeneralQuizMultipleChoiceUseCase constructor.
     * @param MultipleChoiceRepositoryInterface $multipleChoiceRepository
     * @param QuestionBankRepositoryInterface $questionBankRepository
     */
    public function __construct(
        MultipleChoiceRepositoryInterface $multipleChoiceRepository,
        QuestionBankRepositoryInterface $questionBankRepository,
        AddQuestionBankToGeneralQuiz $addQuestionBankToGeneralQuiz
    )
    {
        $this->multipleChoiceRepository = $multipleChoiceRepository;
        $this->questionBankRepository = $questionBankRepository;
        $this->addQuestionBankToGeneralQuiz = $addQuestionBankToGeneralQuiz;
    }

    public function addQuestion(GeneralQuiz $generalQuiz, $data)
    {

        $generalQuizQuestionsData = $data->generalQuizQuestionsData;

        $multipleChoiceType = Option::query()->where("slug", "=", $data->question_slug)->first();
        $this->multipleChoiceType = $multipleChoiceType;

        $questions = $generalQuizQuestionsData->questions;
        if ($data->question_slug == QuestionsTypesEnums::SINGLE_CHOICE) {
            $error = $this->validateMCQSignleChoiceQuestions($generalQuizQuestionsData, $generalQuiz);
            if ($error) {
                return $error;
            }
        }

        if ($data->question_slug == QuestionsTypesEnums::MULTI_CHOICE) {
            $error = $this->validateMultipleChoicesQuestions($generalQuizQuestionsData, $generalQuiz);
            if ($error) {
                return $error;
            }
        }
        $multipleChoiceData = [
            'description' => $generalQuizQuestionsData->description,
            'multiple_choice_type' => $multipleChoiceType->id ?? null,
        ];

        $generalQuizQuestionsDataId = $data->generalQuizQuestionsData->getId();

        if (Str::contains($generalQuizQuestionsDataId, 'new')) {
            $multipleChoice = $this->multipleChoiceRepository->create($multipleChoiceData);
        } else {
            $multipleChoice = $this->multipleChoiceRepository->findOrFail($generalQuizQuestionsDataId);
        }

        $multipleChoiceRepo = new MultipleChoiceRepository($multipleChoice);

        $multipleChoiceRepo->update(['description' => $generalQuizQuestionsData->description]);

        $this->deleteQuestions($multipleChoiceRepo, $questions);

        $this->createOrUpdateQuestions($generalQuiz, $multipleChoiceRepo, $multipleChoice->id, $questions,$data);

        return $multipleChoice;
    }

    private function validateMCQSignleChoiceQuestions($generalQuizQuestionsData, GeneralQuiz $generalQuiz)
    {

        $errors = null;

        if (!$generalQuizQuestionsData->description ||  $generalQuizQuestionsData->description == '' || $generalQuizQuestionsData->description == ' ') {
            $errors['errors'][] = [
                'status' => 422,
                'title' => 'description_is_missing',
                'detail' => trans('validation.required', ['attribute'=>trans('general_quizzes.description')])
            ];
        }

        $questions = $generalQuizQuestionsData->questions;

        foreach ($questions as $key => $question) {
            if (!isset($question->question) ||  $question->question == '' || $question->question == ' ') {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans('general_quizzes.question')])
                ];
            }

            if (!isset($question->question_feedback)||  $question->question_feedback == '' || $question->question_feedback== ' ') {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_feedback_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans('general_quizzes.question_feedback')])
                ];
            }

            if (!isset($question->public_status) || !is_bool($question->public_status)) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'public_status_is_missing',
                    'detail' => trans('public_status.required', ['num' => $key + 1,'field'=>trans('public_status.question')])
                ];
            }
            if (!isset($question->options) ) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'options required',
                    'detail' => trans('general_quizzes.options required')
                ];
            }

            if (isset($question->options) && count($question->options) < 2) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'there has to be 2 options or more',
                    'detail' => trans('general_quizzes.there has to be 2 or more options', ['num' => $key + 1])
                ];
            }

            $correct_answers = isset($question->options) ? array_filter(collect($question->options)->pluck('is_correct_answer')->toArray()): [];
            if (count($correct_answers) > 1 or empty($correct_answers)) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'Select one true option',
                    'detail' => trans('general_quizzes.only one true option', ['num' => $key + 1])
                ];
            }
            if (isset($question->options) && count($question->options) > 0) {
                foreach ($question->options as $option) {
                    if (!property_exists($option, 'option') || $option->option == '' || ctype_space($option->option)) {
                        $errors['errors'][] = [
                            'status' => 422,
                            'title' => 'option_is_missing',
                            'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans('general_quizzes.option')])
                        ];
                    }
                }
            }

        }
        return $errors;
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
        GeneralQuiz $generalQuiz,
        MultipleChoiceRepository $multipleChoiceRepo,
        $multipleChoiceId,
        $questions,
        $data
    )
    {
        foreach ($questions as $question) {
            $questionId = $question->id;
            $public_status = $this->addQuestionBankToGeneralQuiz->getQuestionPublicStatus($generalQuiz , $question);
            $questionData = [
                'question' => $question->question ?? null,
                'url' => $question->url ?? '',
                'question_feedback' => $question->question_feedback ?? null,
                'res_multiple_choice_data_id' => $multipleChoiceId,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
                'model' => QuestionModelsEnums::GENERAL_QUIZ,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];

            $questionBankData = [
                'question_type' => MultipleChoiceQuestion::class,
                'school_account_branch_id' => $generalQuiz->branch_id ?? null,
                'school_account_id' => $generalQuiz->school_account_id ?? null,
                'subject_format_subject_id' => $data->section_id,
                'grade' => $data->grade,
                'public_status' => $public_status,
                'subject_id' => $generalQuiz->subject_id ?? null,
            ];

            if ($this->multipleChoiceType) {
                $questionBankData['slug'] = $this->multipleChoiceType->slug;
            }

            if (Str::contains($questionId, 'new')) {
                $questionObj = $multipleChoiceRepo->createQuestion($questionData);

                $questionBankData['question_id'] = $questionObj->id;

                $this->addToQuestionBank($generalQuiz, $questionBankData);
            } else {
                $questionObj = $multipleChoiceRepo->updateQuestion($questionId, $questionData);

                $questionBankData['question_id'] = $questionObj->id;

                $this->updateQuestionBank($data["id"], $questionBankData);
            }

            $this->createOrUpdateOptions($multipleChoiceRepo, $questionObj->id, $question);

            attachQuestionMeida($question, $questionObj, 'subject/multiple_choice');
            attachQuestionVideo($question, $questionObj, 'subject/true_false');
            attachQuestionAudio($question, $questionObj, 'subject/true_false');
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
            $insert = $multipleChoiceRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions(MultipleChoiceRepository $multipleChoiceRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $multipleChoiceRepo->getQuestionOptionsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $multipleChoiceRepo->deleteOptions($questionId, $deleteIds);

    }

    private function addToQuestionBank(GeneralQuiz $generalQuiz, array $questionBankData)
    {
        $question_bank=$this->questionBankRepository->create($questionBankData);
        $generalQuiz->questions()->attach($question_bank->id);
    }

    private function updateQuestionBank(int $id, array $questionBankData)
    {
        $this->questionBankRepository->update($id, $questionBankData);
    }

    public function validateMultipleChoicesQuestions($generalQuizQuestionsData, GeneralQuiz $generalQuiz)
    {
        $errors = null;

        if (!$generalQuizQuestionsData->description ||  $generalQuizQuestionsData->description == '' || $generalQuizQuestionsData->description == ' ') {
            $errors['errors'][] = [
                'status' => 422,
                'title' => 'description_is_missing',
                'detail' => trans('validation.required', ['attribute'=>trans('general_quizzes.description')])
            ];
        }

        $questions = $generalQuizQuestionsData->questions;

        foreach ($questions as $key => $question) {
            if (empty($question?->question)) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans('general_quizzes.question')])
                ];
            }

            if (!isset($question->question_feedback)||  $question->question_feedback == '' || $question->question_feedback== ' ') {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_feedback_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans('general_quizzes.question_feedback')])
                ];
            }


            if (!isset($question->public_status) || !is_bool($question->public_status)) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'public_status_is_missing',
                    'detail' => trans('public_status.required', ['num' => $key + 1,'field'=>trans('public_status.question')])
                ];
            }
            if (!isset($question->options) ) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'options required',
                    'detail' => trans('general_quizzes.options required')
                ];
            }
            if (isset($question->options) && count($question->options) < 2) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'there has to be 2 options',
                    'detail' => trans('general_quizzes.there has to be 2 or more options', ['num' => $key + 1])
                ];
            }
            $correct_answers = isset($question->options) ? collect($question->options)->pluck('is_correct_answer')->toArray(): [];
            if (count(array_intersect([true], $correct_answers)) < 1) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'Select only true option',
                    'detail' => trans('general_quizzes.there has to be at least true option', ['num' => $key + 1])
                ];
            }
            if (isset($question->options) && count($question->options) > 0) {
                foreach ($question->options as $option) {
                    if (!property_exists($option, 'option') || $option->option == '' || ctype_space($option->option)) {
                        $errors['errors'][] = [
                            'status' => 422,
                            'title' => 'option_is_missing',
                            'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans('general_quizzes.option')])
                        ];
                    }
                }
            }

        }

        return $errors;

    }

}
