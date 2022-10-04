<?php

namespace App\OurEdu\QuestionReport\UseCases\FillResource\Questions\CompleteUseCase;

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

    public function fillResource(int $resourceSubjectFormatId, $data)
    {
        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $acceptanceCriteria = json_decode($this->resourceSubjectFormatSubjectRep->findOrFail($resourceSubjectFormatId)->accept_criteria, true);

        $questions = $resourceSubjectFormatSubjectData->questions;
        $completeData = [
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
        ];


        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $complete = $this->completeRepository->getCompleteDataBySubjectFormatId($resourceSubjectFormatId);
            if (!$complete) {
                $complete = $this->completeRepository->create($completeData);
            }
        } else {
            $complete = $this->completeRepository->findOrFail($resourceSubjectFormatSubjectDataId);
        }

        $completeRepo = new CompleteRepository($complete);

        $completeRepo->update(['description'=>$resourceSubjectFormatSubjectData->description]);

        $this->deleteQuestions($completeRepo, $questions);

        $this->createOrUpdateQuestions($completeRepo, $complete->id, $questions);
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
                'res_complete_data_id' => $completeId,
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
}
