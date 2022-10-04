<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases;

use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepositoryInterface;

use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GeneralQuizDragDropUseCase implements GeneralQuizDragDropUseCaseInterface
{
    private $dragDropRepository;
    private $questionBankRepository;
    private $dragDropType;
    private $slug;
    private $addQuestionBankToGeneralQuiz;


    public function __construct(DragDropRepositoryInterface $dragDropRepository,
                                QuestionBankRepositoryInterface $questionBankRepository,
                                AddQuestionBankToGeneralQuiz $addQuestionBankToGeneralQuiz
    )
    {
        $this->dragDropRepository = $dragDropRepository;
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
        $error = $this->validateDragDropQuestions($data, $generalQuiz);
        if ($error) {
            return $error;
        }
        $generalQuizQuestionsDataId = $data->generalQuizQuestionsData->getId();
        $questions = $data->generalQuizQuestionsData->questions;
        $options = $data->generalQuizOptionData->options;
        $public_status = $this->addQuestionBankToGeneralQuiz->getQuestionPublicStatus($generalQuiz , $questions[0]);

        if ($data->question_slug == QuestionsTypesEnums::DRAG_DROP_TEXT) {
            $this->slug = QuestionsTypesEnums::DRAG_DROP_TEXT;
            $this->dragDropType = Option::query()
                ->where("slug", "=", 'text')
                ->where("type", "=", 'drag_drop_drag_drop_type')
                ->first();
        }
        if ($data->question_slug == QuestionsTypesEnums::DRAG_DROP_IMAGE) {
            $this->slug = QuestionsTypesEnums::DRAG_DROP_IMAGE;
            $this->dragDropType = Option::query()
                ->where("slug", "=", 'image')
                ->where("type", "=", 'drag_drop_drag_drop_type')
                ->first();
        }
        $dragDropDataFromRequest = [
            'description' => $generalQuizQuestionsData->description,
            'drag_drop_type' => $this->dragDropType->id,
            'question_feedback' => $generalQuizQuestionsData->question_feedback,
            'model' => QuestionModelsEnums::GENERAL_QUIZ,
        ];

        $questionBankData = [
            'question_type' => DragDropData::class,
            'school_account_branch_id' => $generalQuiz->branch_id ?? null,
            'school_account_id' => $generalQuiz->school_account_id ?? null,
            'subject_format_subject_id' => $data->section_id,
            'grade' => $data->grade,
            'public_status' => $public_status,
            'subject_id' => $generalQuiz->subject_id ?? null,
        ];
        if ($this->slug) {
            $questionBankData['slug'] = $this->slug;
        }
        if (Str::contains($generalQuizQuestionsDataId, 'new')) {
            $dragDropData = $this->dragDropRepository->create($dragDropDataFromRequest);

            $questionBankData['question_id'] = $dragDropData->id;

            $this->addToQuestionBank($generalQuiz, $questionBankData);
        } else {
            $dragDropData = $this->dragDropRepository->findOrFail($generalQuizQuestionsDataId);

            $dragDropData->update($dragDropDataFromRequest);

            $questionBankData['question_id'] = $dragDropData->id;

            $this->updateQuestionBank($data['id'], $questionBankData);
        }



        $dragDropRepo = new DragDropRepository($dragDropData);

        $this->deleteOptions($dragDropRepo, $options);

        $optionPairIds = $this->createOrUpdateOptions($dragDropRepo, $dragDropData->id, $options);

        //////Questions
        $this->deleteQuestions($dragDropRepo, $questions);

        $this->createOrUpdateQuestions($generalQuiz, $dragDropRepo, $dragDropData->id, $questions, $optionPairIds);

        return $dragDropData;
    }

    public function validateDragDropQuestions($data, GeneralQuiz $generalQuiz)
    {

        $errors = [];
        $dragDropData = $data->generalQuizQuestionsData;
        $questions = $data->generalQuizQuestionsData->questions;
        $options = $data->generalQuizOptionData->options;

        if (empty($dragDropData->question_feedback) || $dragDropData->question_feedback == '' || ctype_space($dragDropData->question_feedback)) {
            $errors['errors'][] = [
                'status' => 422,
                'title' => 'question_feedback required',
                'detail' => trans('general_quizzes.is_required', ['field' => trans("general_quizzes.question_feedback")])
            ];
        }

        if (empty($dragDropData->description) || $dragDropData->description == '' || ctype_space($dragDropData->description)) {
            $errors['errors'][] = [
                'status' => 422,
                'title' => 'description required',
                'detail' => trans('general_quizzes.is_required', ['field' => trans("general_quizzes.description")])
            ];
        }

        if (count($questions) <= 0 ) {
            $errors["errors"][] = [
                "status" => 422,
                'title' => "questions options",
                'detail' => trans('general_quizzes.you have to insert at least one question'),
            ];
        }

        $questionsAnswersKeys = [];
        foreach ($questions as $key => $question) {
            if (!isset($question->public_status) || !is_bool($question->public_status)) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'public_status_is_missing',
                    'detail' => trans('public_status.required', ['num' => $key + 1,'field'=>trans('public_status.question')])
                ];
            }
            $questionsAnswersKeys[] = $question->answers;

            if (!property_exists($question, 'answers') || $question->answers == '') {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'answer required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans("general_quizzes.answer")])
                ];
            }
            if (!property_exists($question, 'question') || $question->question == '' || ctype_space($question->question)) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question question',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans("general_quizzes.question")])
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

            if (strpos($question->question, "*__*") ==false) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question question',
                    'detail' => trans('general_quizzes.you have to specify the answer place')
                ];
            }

        }

        $optionsKeys = [];
        foreach ($options as $key => $option) {
            if (!property_exists($option, 'option') || $option->option == '' || ctype_space($option->option)) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'option required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans("general_quizzes.option")])
                ];
            }
            $optionsKeys[] = $option->id;
        }
        if (count($options) <= 0 || count(array_intersect($optionsKeys, $questionsAnswersKeys)) < count($questionsAnswersKeys)) {
            $errors["errors"][] = [
                "status" => 422,
                'title' => "questions options",
                'detail' => trans('general_quizzes.you have to insert the answers to Questions'),
            ];
        }
        return $errors;
    }

    private function createOrUpdateQuestions(GeneralQuiz $generalQuiz, DragDropRepository $dragDropRepo, $dragDropId, $questions, $optionPairIds)
    {
        foreach ($questions as $question) {

            $questionId = $question->id;
            $questionAnswers = $optionPairIds[$question->answers] ?? null;
            $questionData = [
                'question' => $question->question ?? null,
                'image' => $question->image ?? null,
                'res_drag_drop_data_id' => $dragDropId,
                'correct_option_id' => $questionAnswers,
            ];
            if (Str::contains($questionId, 'new')) {

                $questionDataObj = $dragDropRepo->createQuestion($questionData);
            } else {
                $questionDataObj = $dragDropRepo->updateQuestion($questionId, $questionData);
            }

            attachQuestionMeida($question, $questionDataObj, 'subject/drag_drop');

        }

    }

    private function addToQuestionBank(GeneralQuiz $generalQuiz, array $data)
    {
        $question_bank = $this->questionBankRepository->create($data);

        $generalQuiz->questions()->attach($question_bank->id);
    }

    private function updateQuestionBank(int $id, array $data)
    {
        $question_bank = $this->questionBankRepository->update($id, $data);
    }

    private function deleteQuestions(DragDropRepository $dragDropRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $dragDropRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);

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

}
