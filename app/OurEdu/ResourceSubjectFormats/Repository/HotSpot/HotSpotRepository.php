<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\HotSpot;



use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;

class HotSpotRepository implements HotSpotRepositoryInterface
{
    private $hotSpotData;

    public function __construct(HotSpotData $hotSpotData)
    {
        $this->hotSpotData = $hotSpotData;
    }

    public function findOrFail(int $id): ?HotSpotData
    {
       return $this->hotSpotData->findOrFail($id);
    }

    public function create(array $data): HotSpotData
    {
        return $this->hotSpotData->create($data);
    }

    public function update(array $data): bool
    {
        return $this->hotSpotData->update($data);
    }

    public function createQuestion($data): HotSpotQuestion
    {
        return $this->hotSpotData->questions()->create($data);
    }

    public function updateQuestion($questionId, $data): ?HotSpotQuestion
    {
        $this->hotSpotData->questions()->find($questionId)->update($data);

        return $this->hotSpotData->questions()->find($questionId);
    }

    public function deleteQuestionsIds($deleteIds)
    {
        return $this->hotSpotData->questions()->whereIn( 'id', $deleteIds)->delete();
    }

    public function getAnswersIds($questionId)
    {
        return $this->hotSpotData->questions()->find($questionId)->answers()->pluck('id')->toArray();
    }

    public function deleteAnswersIds($deleteIds)
    {
       return HotSpotAnswer::whereIn('id' , $deleteIds)->delete();
    }

    public function insertAnswer($data): HotSpotAnswer
    {
        return HotSpotAnswer::create($data);
    }

    public function updateAnswer($answerId, $data): ?HotSpotAnswer
    {
        HotSpotAnswer::findOrFail($answerId)->update($data);
        return HotSpotAnswer::findOrFail($answerId);
    }

    public function findAnswer($answerId): ?HotSpotAnswer
    {
        return HotSpotAnswer::findOrFail($answerId);
    }

    public function getQuestionsIds()
    {
        return $this->hotSpotData->questions()->pluck('id')->toArray();
    }

    public function findQuestionOrFail(int $id): ?HotSpotQuestion
    {
        return HotSpotQuestion::findOrFail($id);
    }
}
