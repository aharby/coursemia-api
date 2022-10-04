<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice;

use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;

class MultipleChoiceRepository implements MultipleChoiceRepositoryInterface
{


    private $multipleChoiceData;

    public function __construct(MultipleChoiceData $multipleChoiceData)
    {
        $this->multipleChoiceData = $multipleChoiceData;
    }

    /**
     * @param int $id
     * @return MultipleChoiceData|null
     */
    public function findOrFail(int $id): ?MultipleChoiceData
    {
        return $this->multipleChoiceData->findOrFail($id);
    }

    public function findQuestionOrFail(int $id) :? MultipleChoiceQuestion{
        return MultipleChoiceQuestion::findOrFail($id);
    }
    public function createQuestion($data)
    {
        return $this->multipleChoiceData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data)
    {
        $update = $this->multipleChoiceData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->multipleChoiceData->questions()->where('id', $questionId)->firstOrFail();

        }
        return null;
    }

    public function updateQuestionWithoutData($questionId, $data)
    {
        $update = MultipleChoiceQuestion::where('id', $questionId)->update($data);

        if ($update) {
            return MultipleChoiceQuestion::where('id', $questionId)->firstOrFail();

        }
        return null;
    }
    public function getQuestionsIds()
    {
        return $this->multipleChoiceData->questions()->pluck('id')->toArray();

    }

    public function deleteQuestionsIds(array $questionsId)
    {
        return $this->multipleChoiceData->questions()->whereIn('id', $questionsId)->delete();
    }

    public function insertMultipleOptions($data)
    {
        return MultipleChoiceOption::insert($data);
    }

    public function updateOption($optionsId, $data)
    {
        return MultipleChoiceOption::where('id', $optionsId)->update($data);
    }


    public function getQuestionOptionsIds($questionId)
    {
        $question = $this->multipleChoiceData->questions()->find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }
    public function getQuestionOptionsIdsWithoutData($questionId)
    {
        $question =  MultipleChoiceQuestion::find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteOptions($questionId, $optionsIds)
    {
        $question = $this->multipleChoiceData->questions()->find($questionId);

        if ($question) {
            return $question->options()->whereIn('id', $optionsIds)->delete();
        }
        return false;
    }

    public function deleteOptionsWithoutData($questionId, $optionsIds)
    {
        $question = MultipleChoiceQuestion::find($questionId);

        if ($question) {
            return $question->options()->whereIn('id', $optionsIds)->delete();
        }
        return false;
    }

    public function deleteAllOptions($questionId)
    {
        $question = MultipleChoiceQuestion::find($questionId);

        if ($question) {
            return $question->options()->delete();
        }
        return false;
    }

    public function deleteQuestionWithoutData($questionId)
    {
        $question = MultipleChoiceQuestion::find($questionId);

        if ($question) {
            return $question->delete();
        }
        return false;
    }

    /**
     * @param array $data
     * @return MultipleChoiceData
     */
    public function create(array $data): MultipleChoiceData
    {
        return $this->multipleChoiceData->create($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        return $this->multipleChoiceData->update($data);
    }

    /**
     * @param $resourceSubjectFormatSubjectId
     * @return MultipleChoiceData|null
     */
    public function getMultipleChoiceDataBySubjectFormatId($resourceSubjectFormatSubjectId): ?MultipleChoiceData
    {
        return $this->multipleChoiceData->where('resource_subject_format_subject_id',
            $resourceSubjectFormatSubjectId)->first();
    }


    public function updateSingleQuestion(int $id , $data)
    {
        return MultipleChoiceQuestion::where('id' , $id)->update($data);
    }

    public function getSingleQuestion(int $id)
    {
        return MultipleChoiceQuestion::findOrFail($id);
    }
}
