<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\DragDrop;


use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use Symfony\Component\Console\Question\Question;

class DragDropRepository implements DragDropRepositoryInterface
{
    private $dragDropData;

    public function __construct(DragDropData $dragDropData)
    {
        $this->dragDropData = $dragDropData;
    }

    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?DragDropData
    {
        return $this->dragDropData->findOrFail($id);
    }

    public function findQuestionOrFail(int $id): ?DragDropQuestion
    {
        return DragDropQuestion::findOrFail($id);
    }

    public function update(array $data)
    {
        return $this->dragDropData->update($data);
    }

    public function create(array $data): DragDropData
    {
        return $this->dragDropData->create($data);
    }

    public function getDragDropDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?DragDropData
    {
        return $this->dragDropData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }

    public function createQuestion($data)
    {
        return $this->dragDropData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data): ?DragDropQuestion
    {
        $update = $this->dragDropData->questions()->where('id', $questionId)->update($data);

        if ($update) {
            return $this->dragDropData->questions()->where('id', $questionId)->firstOrFail();

        }
        return null;
    }

    public function getQuestionsIds()
    {
        return $this->dragDropData->questions()->pluck('id')->toArray();

    }

    public function deleteQuestionsIds(array $questionsId)
    {
        return $this->dragDropData->questions()->whereIn('id', $questionsId)->delete();
    }


    public function createOption($data)
    {
        return $this->dragDropData->options()->create($data);
    }

    public function updateOption($questionId, $data): ?DragDropOption
    {
        $update = $this->dragDropData->options()->where('id', $questionId)->update($data);
        if ($update) {
            return $this->dragDropData->options()->where('id', $questionId)->firstOrFail();

        }
        return null;
    }

    public function getOptionsIds()
    {
        return $this->dragDropData->options()->pluck('id')->toArray();

    }

    public function deleteOptionsIds(array $questionsId)
    {
        return $this->dragDropData->options()->whereIn('id', $questionsId)->delete();
    }

    public function getDragDropData()
    {
        return $this->dragDropData;
    }
}
