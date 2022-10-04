<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;


use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepositoryInterface;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class GeneralTrueFalseUseCase implements GeneralTrueFalseUseCaseInterface
{
    private $trueFalseRepository;
    private $questionBankRepository;
    private $addQuestionBankToGeneralQuiz;


    public function __construct(
        TrueFalseRepositoryInterface $trueFalseRepository,
        QuestionBankRepositoryInterface  $questionBankRepository,
        AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz
    )
    {
        $this->trueFalseRepository = $trueFalseRepository;
        $this->questionBankRepository = $questionBankRepository;
        $this->addQuestionBankToGeneralQuiz =$addQuestionBankToGeneralQuiz;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */

    public function addQuestion(GeneralQuiz $generalQuiz, $data)
    {
        $generalQuizQuestionsData = $data->generalQuizQuestionsData;
        $trueFalseype = Option::query()->where("slug", "=", $data->question_slug)->first();

        $questions = $generalQuizQuestionsData->questions;

        $error = $this->validateTrueFalseQuestions($generalQuizQuestionsData , $generalQuiz);

        if($error){
            return $error;
        }
        $trueFalseData = [
            'description' => $generalQuizQuestionsData->description,
            'true_false_type' => $trueFalseype->id ?? null,
        ];

        $generalQuizQuestionsDataId = $data->generalQuizQuestionsData->getId();

        if (Str::contains($generalQuizQuestionsDataId, 'new')) {
            $trueFalse = $this->trueFalseRepository->create($trueFalseData);
        }
        else {
            $trueFalse = $this->trueFalseRepository->findOrFail($generalQuizQuestionsDataId);
        }

        $trueFalseRepo = new TrueFalseRepository($trueFalse);

        $trueFalseRepo->update(['description'=>$generalQuizQuestionsData->description]);

        $this->deleteQuestions($trueFalseRepo, $questions);

        $this->createOrUpdateQuestions($generalQuiz, $trueFalseRepo, $trueFalse->id, $questions,$data);

        return $trueFalse;
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

    private function createOrUpdateQuestions(
        GeneralQuiz $generalQuiz,
        TrueFalseRepository $trueFalseRepo,
        $true_falseId,
        $questions,
        $data
    ) {
        foreach ($questions as $question) {
            $questionId = $question->id;
            $public_status = $this->addQuestionBankToGeneralQuiz->getQuestionPublicStatus($generalQuiz , $question);
            $questionData = [
                'text' => $question->question ?? null,
                'image' => $question->image ?? null,
                'question_feedback' => $question->question_feedback ?? null,
                'res_true_false_data_id' => $true_falseId,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
                'model' => QuestionModelsEnums::GENERAL_QUIZ,
                'is_true' => (int)$question->is_true,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];

            $questionBank = [
                'question_type' => TrueFalseQuestion::class,
                'school_account_branch_id' => $generalQuiz->branch_id ?? null,
                'school_account_id' => $generalQuiz->school_account_id ?? null,
                'subject_format_subject_id' => $data->section_id,
                'subject_id' => $generalQuiz->subject_id ?? null,
                'slug' => QuestionsTypesEnums::TRUE_FALSE,
                'grade' => $data->grade,
                'public_status' => $public_status,
            ];

            if (Str::contains($questionId, 'new')) {
                $questionObj = $trueFalseRepo->createQuestion($questionData);

                $questionBank['question_id'] = $questionObj->id;

                $this->addToQuestionBank($generalQuiz, $questionBank);
            } else {
                $questionObj = $trueFalseRepo->updateQuestion($questionId, $questionData);

                $questionBank['question_id'] = $questionObj->id;

                $this->updateQuestionBank($data["id"], $questionBank);
            }
            $this->createOrUpdateOptions($trueFalseRepo, $questionObj->id, $question);

            attachQuestionMeida($question, $questionObj, 'subject/true_false');
            attachQuestionVideo($question, $questionObj, 'subject/true_false');
            attachQuestionAudio($question, $questionObj, 'subject/true_false');
        }

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
                'is_correct_answer' => $option->is_correct_answer,
                'res_true_false_question_id' => $questionId,
            ];

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $trueFalseRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert = $trueFalseRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions(TrueFalseRepository $trueFalseRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $trueFalseRepo->getQuestionOptionsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $trueFalseRepo->deleteOptions($questionId, $deleteIds);

    }

    private function addToQuestionBank(GeneralQuiz $generalQuiz, array $data)
    {
        $question_bank=$this->questionBankRepository->create($data);
        $generalQuiz->questions()->attach($question_bank->id);
    }

    private function updateQuestionBank(int $id, array $data)
    {
        $question_bank=$this->questionBankRepository->update($id, $data);
    }

    public function validateTrueFalseQuestions($generalQuizQuestionsData , GeneralQuiz $generalQuiz)
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
        }

        return $errors;
    }
}
