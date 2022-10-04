<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse;


use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use Symfony\Component\Console\Question\Question;

class TrueFalseRepository implements TrueFalseRepositoryInterface
{
    private $trueFalseData;

    public function __construct(TrueFalseData $trueFalseData)
    {
        $this->trueFalseData = $trueFalseData;
    }

    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?TrueFalseData
    {
        return $this->trueFalseData->findOrFail($id);
    }

    public function update(array $data)
    {
        return $this->trueFalseData->update($data);
    }

    public function findQuestionOrFail(int $id): ?TrueFalseQuestion
    {
        return TrueFalseQuestion::findOrFail($id);
    }

    public function create(array $data): TrueFalseData
    {
        return $this->trueFalseData->create($data);
    }

    public function createQuestion($data)
    {
        return $this->trueFalseData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data): ?TrueFalseQuestion
    {
        $update = $this->trueFalseData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->trueFalseData->questions()->where('id', $questionId)->firstOrFail();

        }
        return null;
    }

    public function updateQuestionWithoutData($questionId, $data): ?TrueFalseQuestion
    {
        $update = TrueFalseQuestion::where('id', $questionId)->update($data);

        if ($update) {
            return TrueFalseQuestion::where('id', $questionId)->firstOrFail();
        }
        return null;
    }


    public function deleteQuestionsWithoutData(array $questionIds)
    {
        return TrueFalseQuestion::whereIn('id', $questionIds)->delete();
    }

    public function getQuestionsIds()
    {
        return $this->trueFalseData->questions()->pluck('id')->toArray();

    }

    public function deleteQuestionsIds(array $questionsId)
    {
        return $this->trueFalseData->questions()->whereIn('id', $questionsId)->delete();
    }

    public function insertMultipleOptions($data)
    {
        return TrueFalseOption::insert($data);
    }

    public function updateOption($optionsId, $data)
    {
        return TrueFalseOption::where('id', $optionsId)->update($data);
    }


    public function getQuestionOptionsIds($questionId)
    {
        $question = $this->trueFalseData->questions()->find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }

    public function getQuestionOptionsIdsWithoutData($questionId)
    {
        $question = TrueFalseQuestion::find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteOptions($questionId, $optionsIds)
    {
        $question = $this->trueFalseData->questions()->find($questionId);

        if ($question) {
            return $question->options()->whereIn('id', $optionsIds)->delete();
        }
        return false;
    }

    public function deleteALlQuestionOptions($questionId)
    {
        $question = TrueFalseQuestion::find($questionId);

        if ($question) {
            return $question->options()->delete();
        }
        return false;
    }

    public function deleteQuestionOptions($questionId , $optionIds)
    {
        $question = TrueFalseQuestion::find($questionId);

        if ($question) {
            return $question->options()->whereIn('id' , $optionIds)->delete();
        }
        return false;
    }
}
