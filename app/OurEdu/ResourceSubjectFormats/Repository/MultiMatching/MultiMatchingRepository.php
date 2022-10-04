<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching;

use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingQuestion;

class MultiMatchingRepository implements MultiMatchingRepositoryInterface
{
    private $multiMatchingData;

    public function __construct(MultiMatchingData $multiMatchingData)
    {
        $this->multiMatchingData = $multiMatchingData;
    }


    public function findOrFail(int $id): ?MultiMatchingData
    {
        return $this->multiMatchingData->findOrFail($id);
    }

    public function findQuestionOrFail(int $id): ?MultiMatchingQuestion
    {
        return MultiMatchingQuestion::findOrFail($id);
    }

    public function create(array $data): MultiMatchingData
    {
        return $this->multiMatchingData->create($data);
    }

    public function update(array $data)
    {
        return $this->multiMatchingData->update($data);
    }

    public function createQuestion($data)
    {
        return $this->multiMatchingData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data): ?MultiMatchingQuestion
    {
        $update = $this->multiMatchingData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->multiMatchingData->questions()->where('id', $questionId)->firstOrFail();
        }
        return null;
    }

    public function updateQuestionData($questionId, $data): ?MultiMatchingQuestion
    {
        $update = $this->multiMatchingData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->multiMatchingData->questions()->where('id', $questionId)->firstOrFail();
        }
        return null;
    }

    public function getQuestionsIds()
    {
        return $this->multiMatchingData->questions()->pluck('id')->toArray();
    }

    public function deleteQuestionsIds(array $questionsId)
    {
        return $this->multiMatchingData->questions()->whereIn('id', $questionsId)->delete();
    }
    public function deleteOptionsIds(array $optionsId)
    {
        return $this->multiMatchingData->options()->whereIn('id', $optionsId)->delete();
    }

    public function insertMultipleOptions($data)
    {
        return MultiMatchingOption::insert($data);
    }
    public function insertOption($data)
    {
        return MultiMatchingOption::create($data);
    }

    public function updateOption($optionsId, $data)
    {
        return MultiMatchingOption::findOrFail($optionsId)->update($data);
    }

    public function findOption($optionsId)
    {
        return MultiMatchingOption::findOrFail($optionsId);
    }


    public function getQuestionOptionsIds($questionId)
    {
        $question = $this->multiMatchingData->questions()->find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }

    public function getAllQuestionOptionsIds()
    {
        return $this->multiMatchingData->options()->pluck('id')->toArray();
    }

    public function deleteOptions($optionsIds)
    {
        return $this->multiMatchingData->options()->whereIn('id', $optionsIds)->delete();
    }

    public function getMultiMatchingDataBySubjectFormatId($resourceSubjectFormatSubjectId): ? MultiMatchingData
    {
        return $this->multiMatchingData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }
}
