<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Complete;

use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteAcceptedAnswer;

class CompleteRepository implements CompleteRepositoryInterface
{
    private $completeData;

    public function __construct(CompleteData $completeData)
    {
        $this->completeData = $completeData;
    }

    /**
     * @param int $id
     * @return CompleteData|null
     */
    public function findOrFail(int $id): ?CompleteData
    {
        return $this->completeData->findOrFail($id);
    }

    public function createQuestion($data)
    {
        return $this->completeData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data)
    {
        $update = $this->completeData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->completeData->questions()->where('id', $questionId)->firstOrFail();
        }

        return null;
    }

    public function getQuestionsIds()
    {
        return $this->completeData->questions()->pluck('id')->toArray();
    }

    public function deleteQuestionsIds(array $questionsId)
    {
        return $this->completeData->questions()->whereIn('id', $questionsId)->delete();
    }

    public function insertMultipleAcceptedAnswers($data)
    {
        return CompleteAcceptedAnswer::insert($data);
    }

    public function updateAcceptedAnswer($answersId, $data)
    {
        return CompleteAcceptedAnswer::where('id', $answersId)->update($data);
    }

    public function createOrUpdateAnswer(CompleteQuestion $question, $data)
    {
        $answer = $question->answer;
        $data['res_complete_question_id'] = $question->id;

        if (!$answer) {
            $answer = new CompleteAnswer();
        }

        $answer->fill($data);
        $answer->save();

        return $answer;
    }

    public function getQuestionAcceptedAnswersIds($questionId)
    {
        $question = $this->completeData->questions()->find($questionId);

        if ($question) {
            return $question->acceptedAnswers()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteAcceptedAnswers($questionId, $answersIds)
    {
        $question = $this->completeData->questions()->find($questionId);

        if ($question) {
            return $question->acceptedAnswers()->whereIn('id', $answersIds)->delete();
        }

        return false;
    }

    /**
     * @param array $data
     * @return CompleteData
     */
    public function create(array $data): CompleteData
    {
        return $this->completeData->create($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        return $this->completeData->update($data);
    }

    /**
     * @param $resourceSubjectFormatSubjectId
     * @return CompleteData|null
     */
    public function getCompleteDataBySubjectFormatId($resourceSubjectFormatSubjectId): ?CompleteData
    {
        return $this->completeData->where(
            'resource_subject_format_subject_id',
            $resourceSubjectFormatSubjectId
        )->first();
    }


    public function updateSingleQuestion(int $id, $data)
    {
        return CompleteQuestion::where('id', $id)->update($data);
    }

    public function getSingleQuestion(int $id)
    {
        return CompleteQuestion::findOrFail($id);
    }
    public function getCompleteBySubjectFormatId($resourceSubjectFormatSubjectId): ?CompleteData
    {
        return $this->completeData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }
}
