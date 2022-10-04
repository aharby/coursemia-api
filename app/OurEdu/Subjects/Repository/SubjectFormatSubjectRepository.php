<?php

namespace App\OurEdu\Subjects\Repository;

use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SubjectFormatSubjectRepository implements SubjectFormatSubjectRepositoryInterface
{
    private $subjectFormatSubject;

    public function __construct(SubjectFormatSubject $subjectFormatSubject)
    {
        $this->subjectFormatSubject = $subjectFormatSubject;
    }

    /**
     * @param int $id
     * @return SubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?SubjectFormatSubject
    {
        return $this->subjectFormatSubject->findOrFail($id);
    }

    public function filterActiveIds(array $ids): ?array
    {
        return $this->subjectFormatSubject->whereIn('id', $ids)->pluck('id')->toArray();
    }

    public function toggleActive()
    {
        $this->subjectFormatSubject->is_active = ! $this->subjectFormatSubject->is_active;

        $this->subjectFormatSubject->save();
    }

    public function getSectionResources($sectionID) {

        return $this->subjectFormatSubject->findOrFail($sectionID)->resourceSubjectFormatSubject;
    }

    public function getSectionsByIds($sectionIds)
    {
        return $this->subjectFormatSubject
            ->whereIn('id', $sectionIds)
            ->orderBy('list_order_key', 'DESC')
            ->get();
    }

    public function getSectionTasks($section, $onlyNotAssigned)
    {
        $tasks = [];
        $resourceSubjectFormatSubject = $section->resourceSubjectFormatSubject;

        foreach ($resourceSubjectFormatSubject as $value) {
            if ($onlyNotAssigned) {
                if ($value->task()->where('is_assigned', 0)->exists()) {
                    $tasks[] = $value->task()->where('is_assigned', 0)->first();
                }
            } else {
                if ($value->task->exists()) {
                    $tasks[] = $value->task->first();
                }
            }
        }
        return $tasks;
    }

}
