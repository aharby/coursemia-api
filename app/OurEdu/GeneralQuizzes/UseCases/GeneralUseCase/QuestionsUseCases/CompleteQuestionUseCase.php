<?php

namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\ResourceSubjectFormats\Repository\Complete\CompleteRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepository;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\CompleteQuestionUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
class CompleteQuestionUseCase implements CompleteQuestionUseCaseInterface
{
    private $completeRepository;
    private $questionBankRepository;
    private $addQuestionBankToGeneralQuiz;


    public function __construct(
        CompleteRepository $completeRepository,
        QuestionBankRepositoryInterface  $questionBankRepository,
        AddQuestionBankToGeneralQuiz $addQuestionBankToGeneralQuiz
    )
    {
        $this->completeRepository = $completeRepository;
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

        $error = $this->validateCompleteQuestions($generalQuizQuestionsData , $generalQuiz);
        if($error){
            return $error;
        }


        $completeData = [
            'description' => $generalQuizQuestionsData->description,
        ];

        $generalQuizQuestionsDataId = $data->generalQuizQuestionsData->getId();

        if (Str::contains($generalQuizQuestionsDataId, 'new')) {
            $complete = $this->completeRepository->create($completeData);
        }
        else {
            $complete = $this->completeRepository->findOrFail($generalQuizQuestionsDataId);
        }

        $completeRepo = new CompleteRepository($complete);

        $completeRepo->update(['description'=>$generalQuizQuestionsData->description]);

        $this->deleteQuestions($completeRepo, $questions);

        $this->createOrUpdateQuestions($generalQuiz, $completeRepo, $complete->id, $questions,$data);

        return $complete;
    }

    private function deleteQuestions(CompleteRepository $completeRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $completeRepo->getQuestionsIds();

        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        $completeRepo->deleteQuestionsIds($deleteIds);
    }

    private function createOrUpdateQuestions(
        GeneralQuiz $generalQuiz,
        CompleteRepository $completeRepo,
        $completeId,
        $questions,
        $data
    ) {
        foreach ($questions as $question) {
            $questionId = $question->id;
            $public_status = $this->addQuestionBankToGeneralQuiz->getQuestionPublicStatus($generalQuiz , $question);
            $questionData = [
                'question' => trim($question->question) ?? null,
                'res_complete_data_id' => $completeId,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
                'model' => QuestionModelsEnums::GENERAL_QUIZ,
                'question_feedback' => $question->question_feedback ?? null,
            ];

            $questionBankData = [
                'question_type' => CompleteQuestion::class,
                'school_account_branch_id' => $generalQuiz->branch_id ?? null,
                'school_account_id' => $generalQuiz->school_account_id ?? null,
                'subject_format_subject_id' => $data->section_id,
                'subject_id' => $generalQuiz->subject_id ?? null,
                'slug' => QuestionsTypesEnums::COMPLETE,
                'public_status' => $public_status,
                'grade' => $data->grade,
            ];

            if (Str::contains($questionId, 'new')) {
                $questionObj = $completeRepo->createQuestion($questionData);

                $questionBankData['question_id'] = $questionObj->id;

                $this->addToQuestionBank($generalQuiz, $questionBankData);
            } else {
                $questionObj = $completeRepo->updateQuestion($questionId, $questionData);

                $questionBankData['question_id'] = $questionObj->id;

                $this->updateQuestionBank($data['id'], $questionBankData);
            }

            if (! $questionObj) {
                throw new ErrorResponseException(trans('api.Uknown question id ' . $questionId));
            }

            $this->createOrUpdateMainAnswer($completeRepo, $questionObj, $question);

            $this->createOrUpdateAcceptedAnswers($completeRepo, $questionObj->id, $question);
        }
    }

    protected function createOrUpdateMainAnswer(CompleteRepository $completeRepo, $questionObj, $question)
    {
        $answerData = [
                'answer' => trim(strip_tags($question->answer)) ?? null,
            ];

        $completeRepo->createOrUpdateAnswer($questionObj, $answerData);
    }

    private function createOrUpdateAcceptedAnswers(CompleteRepository $completeRepo, $questionId, $question)
    {
        $accepted_answers = $question->accepted_answers ?? [];

        $accepted_answersDataMultiple = [];
        $this->deleteAcceptedAnswers($completeRepo, $questionId, $accepted_answers);

        foreach ($accepted_answers as $answer) {
            $answerId = $answer->id;
            $answerData = [
                'answer' => trim(strip_tags($answer->answer)),
                'res_complete_question_id' => $questionId,
            ];


            if (Str::contains($answerId, 'new')) {
                $accepted_answersDataMultiple[] = $answerData;
            } else {
                $completeRepo->updateAcceptedAnswer($answerId, $answerData);
            }
        }

        if (count($accepted_answersDataMultiple) > 0) {
            $insert = $completeRepo->insertMultipleAcceptedAnswers($accepted_answersDataMultiple);
        }
    }

    private function deleteAcceptedAnswers(CompleteRepository $completeRepo, $questionId, $accepted_answers)
    {
        $newIds = Arr::pluck($accepted_answers, 'id');
        $oldQuestionsIds = $completeRepo->getQuestionAcceptedAnswersIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $completeRepo->deleteAcceptedAnswers($questionId, $deleteIds);
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

    private function validateCompleteQuestions($generalQuizQuestionsData , GeneralQuiz $generalQuiz)
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
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans("general_quizzes.question")])
                ];
            }
            $contains = Str::contains($question->question, '*__*');

            if(!$contains){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => '*__* is required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans('general_quizzes.you should add *__*  to question')])
                ];
            }
            $question_data = trim($question->question,'*__*');
            if(strlen(trim($question_data,' '))==0){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question text is required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans("general_quizzes.question")])
                ];

            }
            if (!isset($question->question_feedback) ||  $question->question_feedback == '' || $question->question_feedback== ' ') {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_feedback_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field'=>trans('general_quizzes.question_feedback')])
                ];
            }

            if (!$question->answer ||  $question->answer == '' || $question->answer== ' ' || strlen(trim($question->answer,' ')) == 0) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_main_answer_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field'=>trans('general_quizzes.answer')])
                ];
            }

            foreach ($question->accepted_answers as $answer) {
                if (!property_exists($answer, 'answer') || $answer->answer == '' || ctype_space($answer->answer)) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'answer_is_missing',
                        'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans('general_quizzes.accepted_answers_required')])
                    ];
                }
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
