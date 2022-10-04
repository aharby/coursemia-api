<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;


use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Repository\Essay\EssayRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Essay\EssayRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayQuestion;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class GeneralEssayUseCase implements GeneralEssayCaseInterface
{
    private $essayRepository;
    private $questionBankRepository;
    private $addQuestionBankToGeneralQuiz;


    public function __construct(
        EssayRepositoryInterface $essayRepository,
        QuestionBankRepositoryInterface  $questionBankRepository,
        AddQuestionBankToGeneralQuiz $addQuestionBankToGeneralQuiz
    )
    {
        $this->essayRepository = $essayRepository;
        $this->questionBankRepository = $questionBankRepository;
        $this->addQuestionBankToGeneralQuiz = $addQuestionBankToGeneralQuiz;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */

    public function addQuestion(GeneralQuiz $generalQuiz, $data)
    {
        $generalQuizQuestionsData = $data->generalQuizQuestionsData;


        $questions = $generalQuizQuestionsData->questions;

        $error = $this->validateEssayQuestions($generalQuizQuestionsData , $generalQuiz);

        if($error){
            return $error;
        }
        $essayData = [
            'description' => $generalQuizQuestionsData->description,
        ];

        $generalQuizQuestionsDataId = $data->generalQuizQuestionsData->getId();

        if (Str::contains($generalQuizQuestionsDataId, 'new')) {
            $essay = $this->essayRepository->create($essayData);
        }
        else {
            $essay = $this->essayRepository->findOrFail($generalQuizQuestionsDataId);
        }

        $essayRepo = new EssayRepository($essay);

        $essayRepo->update(['description'=>$generalQuizQuestionsData->description]);

        $this->deleteQuestions($essayRepo, $questions);

        $this->createOrUpdateQuestions($generalQuiz, $essayRepo, $essay->id, $questions,$data);

        return $essay;
    }

    private function deleteQuestions(EssayRepository $essayRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $essayRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        // deleting media data for the deleted questions
        foreach ($deleteIds as $questionId) {
            $questionObj = $essayRepo->findQuestionOrFail($questionId);
            if ($questionObj->media) {
                deleteQuestionMedia($questionObj->media);
            }
        }


        $essayRepo->deleteQuestionsIds($deleteIds);

    }

    private function createOrUpdateQuestions(
        GeneralQuiz $generalQuiz,
        EssayRepository $essayRepo,
        $true_falseId,
        $questions,
        $data
    ) {
        foreach ($questions as $question) {
            $questionId = $question->id;
            $public_status = $this->addQuestionBankToGeneralQuiz->getQuestionPublicStatus($generalQuiz , $question);
            $questionData = [
                'text' => $question->question ?? null,
                //'image' => $question->image ?? null,
                'question_feedback' => $question->question_feedback ?? null,
                'perfect_answers' => $question->perfect_answers ?? null,
                'res_essay_data_id' => $true_falseId,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
                'model' => QuestionModelsEnums::GENERAL_QUIZ,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];

            $questionBankData = [
                'question_type' => EssayQuestion::class,
                'school_account_branch_id' => $generalQuiz->branch_id ?? null,
                'school_account_id' => $generalQuiz->school_account_id ?? null,
                'subject_format_subject_id' => $data->section_id,
                'subject_id' => $generalQuiz->subject_id ?? null,
                'slug' => QuestionsTypesEnums::ESSAY,
                'public_status' => $public_status,
                'grade' => $data->grade,
            ];

            if (Str::contains($questionId, 'new')) {
                $questionObj = $essayRepo->createQuestion($questionData);
                $questionBankData['question_id'] = $questionObj->id;
                $this->addToQuestionBank($generalQuiz, $questionBankData);
            } else {
                $questionObj = $essayRepo->updateQuestion($questionId, $questionData);
                $questionBankData['question_id'] = $questionObj->id;
                $this->updateQuestionBank($data['id'], $questionBankData);
            }

            attachQuestionMeida($question, $questionObj, 'subject/true_false');
            attachQuestionVideo($question, $questionObj, 'subject/true_false');
            attachQuestionAudio($question, $questionObj, 'subject/true_false');
        }

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

    public function validateEssayQuestions($generalQuizQuestionsData , GeneralQuiz $generalQuiz)
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
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field'=>trans('general_quizzes.question_feedback')])
                ];
            }
            if (!$question->perfect_answers ||  $question->perfect_answers == '' || $question->perfect_answers== ' ') {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_prefect_answer_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field'=>trans('general_quizzes.prefect_answer')])
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
