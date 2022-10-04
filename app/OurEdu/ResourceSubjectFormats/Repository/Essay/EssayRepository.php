<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\Essay;


use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayData;
use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

use Symfony\Component\Console\Question\Question;

class EssayRepository implements EssayRepositoryInterface
{
    private $essayData;

    public function __construct(EssayData $essayData)
    {
        $this->essayData = $essayData;
    }

    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?EssayData
    {
        return $this->essayData->findOrFail($id);
    }

    public function update(array $data)
    {
        return $this->essayData->update($data);
    }

    public function findQuestionOrFail(int $id): ?EssayQuestion
    {
        return EssayQuestion::findOrFail($id);
    }

    public function create(array $data): EssayData
    {
        return $this->essayData->create($data);
    }

    public function createQuestion($data)
    {
        return $this->essayData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data): ?EssayQuestion
    {
        $update = $this->essayData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->essayData->questions()->where('id', $questionId)->firstOrFail();

        }
        return null;
    }

    public function updateQuestionWithoutData($questionId, $data): ?EssayQuestion
    {
        $update = EssayQuestion::where('id', $questionId)->update($data);

        if ($update) {
            return EssayQuestion::where('id', $questionId)->firstOrFail();
        }
        return null;
    }


    public function deleteQuestionsWithoutData(array $questionIds)
    {
        return EssayQuestion::whereIn('id', $questionIds)->delete();
    }

    public function getQuestionsIds()
    {
        return $this->essayData->questions()->pluck('id')->toArray();

    }

    public function deleteQuestionsIds(array $questionsId)
    {
        return $this->essayData->questions()->whereIn('id', $questionsId)->delete();
    }

}
