<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository;

use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class ResourceSubjectFormatSubjectRepository implements ResourceSubjectFormatSubjectRepositoryInterface
{
    private $resourceSubjectFormatSubject;

    public function __construct(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        $this->resourceSubjectFormatSubject = $resourceSubjectFormatSubject;
    }

    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?ResourceSubjectFormatSubject
    {
        return $this->resourceSubjectFormatSubject->findOrFail($id);
    }

    public function toggleActive()
    {
        $this->resourceSubjectFormatSubject->is_active = !$this->resourceSubjectFormatSubject->is_active;

        $this->resourceSubjectFormatSubject->save();
    }

    public function getResourcesIdsForBySubjectId($subjectId):?array
    {
        return $this->resourceSubjectFormatSubject::doesntHave('task')->where('subject_id', $subjectId)->pluck('id')->toArray();
    }
}
