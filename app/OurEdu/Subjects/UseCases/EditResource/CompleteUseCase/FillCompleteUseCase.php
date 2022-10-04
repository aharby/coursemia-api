<?php

namespace App\OurEdu\Subjects\UseCases\EditResource\CompleteUseCase;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\ResourceSubjectFormats\Repository\Complete\CompleteRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Complete\CompleteRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepository;

class FillCompleteUseCase implements FillCompleteUseCaseInterface
{
    private $completeRepository;
    private $resourceSubjectFormatSubjectRep;


    public function __construct(CompleteRepositoryInterface $completeRepository, ResourceSubjectFormatSubjectRepository $resourceSubjectFormatSubjectRepository)
    {
        $this->completeRepository = $completeRepository;
        $this->resourceSubjectFormatSubjectRep = $resourceSubjectFormatSubjectRepository;
    }

    public function fillResource(int $resourceSubjectFormatId, $data): array
    {
        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;

        $questions = $resourceSubjectFormatSubjectData->questions;
        $completeData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
        ];

        $errors = $this->validateQuestions($questions);

        if (count($errors)) {
            return $errors;
        }

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {


            $complete = $this->completeRepository->getCompleteDataBySubjectFormatId($resourceSubjectFormatId);

            if ($complete) {
                $matchingRepo = new CompleteRepository($complete);

                $matchingRepo->update($completeData);
            } else {
                $complete = $this->completeRepository->create($completeData);
            }


        } else {
            $complete = $this->completeRepository->findOrFail($resourceSubjectFormatSubjectDataId);

            $completeRepo = new CompleteRepository($complete);

            $completeRepo->update($completeData);
        }
        $completeRepo = new CompleteRepository($complete);

        $this->deleteQuestions($completeRepo, $questions);

        $this->createOrUpdateQuestions($completeRepo, $complete->id, $questions);

        return [
            'status' => 200,
            'detail' => trans('resourceSubjectFormat.Questions created Successfully'),
            'title' => trans('resourceSubjectFormat.Questions created Successfully'),
        ];
    }

    private function deleteQuestions(CompleteRepository $completeRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $completeRepo->getQuestionsIds();

        $deleteIds = array_diff($oldQuestionsIds, $newIds);

        $completeRepo->deleteQuestionsIds($deleteIds);
    }

    private function createOrUpdateQuestions(
        CompleteRepository $completeRepo,
        $completeId,
        $questions
    ) {

        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'question' => $question->question ?? null,
                'question_feedback' => $question->question_feedback ?? null,
                'res_complete_data_id' => $completeId,
                'time_to_solve' => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            ];

            if (Str::contains($questionId, 'new')) {
                $questionObj = $completeRepo->createQuestion($questionData);
            } else {
                $questionObj = $completeRepo->updateQuestion($questionId, $questionData);
            }

            if (! $questionObj) {
                throw new ErrorResponseException(trans('api.Uknown question id ' . $questionId));
            }

            $this->createOrUpdateMainAnswer($completeRepo, $questionObj, $question);

            $this->createOrUpdateAcceptedAnswers($completeRepo, $questionObj->id, $question);
        }
    }

    protected function createOrUpdateMainAnswer($completeRepo, $questionObj, $question)
    {
        $answerData = [
                'answer' => $question->answer ?? null,
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
                'answer' => $answer->answer,
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

    private function validateQuestions($questions): array
    {
        foreach ($questions as $key => $question) {
            if (!$question->question ||  $question->question == '' || $question->question== ' ') {
                return [
                    'status' => 422,
                    'title' => 'question_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans("general_quizzes.question")])
                ];
            }
            $contains = Str::contains($question->question, '*__*');

            if (!$contains) {
                return [
                    'status' => 422,
                    'title' => '*__* is required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans('general_quizzes.you should add *__*  to question')])
                ];
            }
            $question_data = trim($question->question,'*__*');
            if(strlen(trim($question_data,' '))==0){
                return [
                    'status' => 422,
                    'title' => 'question text is required',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1,'field'=>trans("general_quizzes.question")])
                ];

            }
            if (!$question->question_feedback ||  $question->question_feedback == '' || $question->question_feedback== ' ') {
                return [
                    'status' => 422,
                    'title' => 'question_feedback_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field'=>trans('general_quizzes.question_feedback')])
                ];
            }

            if (!$question->answer ||  $question->answer == '' || $question->answer== ' ' || strlen(trim($question->answer,' ')) == 0) {
                return [
                    'status' => 422,
                    'title' => 'question_main_answer_is_missing',
                    'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field'=>trans('general_quizzes.answer')])
                ];
            }

            foreach ($question->accepted_answers as $answer) {
                if (!property_exists($answer, 'answer') || $answer->answer == '' || ctype_space($answer->answer)) {
                    return [
                        'status' => 422,
                        'title' => 'answer_is_missing',
                        'detail' => trans('general_quizzes.required', ['num' => $key + 1, 'field' => trans('general_quizzes.accepted_answers_required')])
                    ];
                }
            }
        }

        return [];
    }
}
