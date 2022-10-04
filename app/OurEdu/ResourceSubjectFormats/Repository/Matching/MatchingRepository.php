<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\Matching;

use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingQuestion;

class MatchingRepository implements MatchingRepositoryInterface
{
    private $matchingData;

    public function __construct(MatchingData $matchingData)
    {
        $this->matchingData = $matchingData;
    }

    public function findOrFail(int $id): ?MatchingData
    {
        return $this->matchingData->findOrFail($id);
    }

    public function findQuestionOrFail(int $id): ?MatchingQuestion
    {
        return MatchingQuestion::findOrFail($id);
    }

    public function create(array $data): MatchingData
    {
        return $this->matchingData->create($data);
    }

    public function update(array $data)
    {
        return $this->matchingData->update($data);
    }

    public function createQuestion($data)
    {
        return $this->matchingData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data): ?MatchingQuestion
    {
        $update = $this->matchingData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->matchingData->questions()->where('id', $questionId)->firstOrFail();
        }
        return null;
    }

    public function updateQuestionData($questionId, $data): ?MatchingQuestion
    {
        $update = $this->matchingData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->matchingData->questions()->where('id', $questionId)->firstOrFail();
        }
        return null;
    }

    public function getQuestionsIds()
    {
        return $this->matchingData->questions()->pluck('id')->toArray();
    }

    public function deleteQuestionsIds(array $questionsId)
    {
        return $this->matchingData->questions()->whereIn('id', $questionsId)->delete();
    }
    public function deleteOptionsIds(array $optionsId)
    {
        return $this->matchingData->options()->whereIn('id', $optionsId)->delete();
    }

    public function insertMultipleOptions($data)
    {
        return MatchingOption::insert($data);
    }

    public function updateOption($optionsId, $data)
    {
        return MatchingOption::where('id', $optionsId)->update($data);
    }


    public function getQuestionOptionsIds()
    {
        return $this->matchingData->options()->pluck('id')->toArray();
    }

    public function deleteOptions($optionsIds)
    {
        return $this->matchingData->options()->whereIn('id', $optionsIds)->delete();
    }
    public function getMatchingDataBySubjectFormatId($resourceSubjectFormatSubjectId): ?MatchingData
    {
        return $this->matchingData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }
}
