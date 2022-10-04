<?php

namespace App\OurEdu\Subjects\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectSubscribe;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use hanneskod\classtools\Iterator\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mpdf\Tag\Sub;

class SubjectRepository implements SubjectRepositoryInterface
{
    use Filterable;
    public $subject;

    public function __construct(Subject $subject)
    {
        $this->subject = $subject;
    }

    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function all(array $filters = [], $sortBy = ''): LengthAwarePaginator
    {
        $model = $this->applyFilters(new Subject(), $filters);
        $model = $model->withCount([
            'exams as practices_count' => function ($query) {
                $query->where('type', \App\OurEdu\Exams\Enums\ExamTypes::PRACTICE);
            },
            'exams as exams_count' => function ($query) {
                $query->where('type', \App\OurEdu\Exams\Enums\ExamTypes::EXAM);
            },
            'exams as average_result' => function ($query) {
                $query->select(DB::raw('coalesce(avg(result),0)'));
            }
        ])->orderByDesc($sortBy);

        return $model->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param  null                 $perPage
     * @param  string               $pageName
     * @param  null                 $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->subject->latest()->paginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param  array                $withCount
     * @param  null                 $perPage
     * @param  string               $pageName
     * @param  null                 $page
     * @return LengthAwarePaginator
     */
    public function paginateWithCount(array $withCount, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->subject->withCount($withCount)->latest()->paginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->subject->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhereContentAuthor(
        int $contentAuthorId,
        $perPage = null,
        $pageName = 'page',
        $page = null
    ): LengthAwarePaginator {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->subject
            ->where('is_active', 1)
            ->whereHas('contentAuthors', function ($q) use ($contentAuthorId) {
                $q->where('id', $contentAuthorId);
            })->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhereStudent(
        array $studentData,
        $perPage = null,
        $pageName = 'page',
        $page = null
    ): LengthAwarePaginator {
        $student = auth()->user()->student;
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        $subjects = $this->subject->where('country_id', $studentData['country_id'])->where(
            'educational_system_id',
            $studentData['educational_system_id']
        )->where(
            'academical_years_id',
            $studentData['academical_years_id']
        )->where('grade_class_id', $studentData['class_id'])->where(
            'is_active',
            1
        );
        if (request()->has('subscribed')) {
            if (request()->boolean('subscribed')) {
                $subjects->whereHas('students', function ($q) use ($student) {
                    $q->where('student_id', "=", $student->id);
                });
            } else {
                $subjects->whereDoesntHave("students", function ($q) use ($student) {
                    $q->where('student_id', "=", $student->id);
                });
            }
        }
        return $subjects->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param  array   $data
     * @return Subject
     */
    public function create(array $data): Subject
    {
        return $this->subject->create($data);
    }

    /**
     * @param  array        $data
     * @return Subject|null
     */
    public function update(array $data): ?Subject
    {
        if ($this->subject->update($data)) {
            return $this->subject->find($this->subject->id);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->subject->delete();
    }

    public function getContentAuthorsIds(): array
    {
        return $this->subject->contentAuthors()->pluck('id')->toArray() ?? [];
    }

    public function getInstructorsIds(): array
    {
        return $this->subject->instructors()->pluck('id')->toArray() ?? [];
    }

    public function attachContentAuthors(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->subject->contentAuthors()->attach($ids);
    }

    public function syncContentAuthors(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->subject->contentAuthors()->sync($ids);
    }

    public function attachInstructors(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->subject->instructors()->attach($ids);
    }

    public function syncInstructors(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->subject->instructors()->sync($ids);
    }

    public function CreateSubjectFormatSubject($data)
    {
        return $this->subject->subjectFormatSubject()->create($data);
    }

    /**
     * @param $subjectFormatSubjectId
     * @return array
     */
    public function getChildrenSubjectFormatSubjectPluckedIds($subjectFormatSubjectId): array
    {
        $subjectFormatSubject = $this->subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            return $subjectFormatSubject->childSubjectFormatSubject()->pluck('id')->toArray() ?? [];
        }
        return [];
    }

    public function getParentSubjectFormatSubject($subjectFormatSubjectId)
    {
        return $this->subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId)->parentSubjectFormatSubject;
    }

    /**
     * @param  array $deleteIds
     * @param  bool  $withIsEditable
     * @return mixed
     */
    public function deleteSubjectFormatSubject(array $deleteIds, $withIsEditable = false)
    {
        $query = $this->subject->subjectFormatSubject();
        if ($withIsEditable) {
            $query = $query->where('is_editable', 1);
        }

        return $query->whereIn('id', $deleteIds)->delete();
    }

    public function updateSubjectFormatSubject(int $subjectFormatSubjectId, array $data)
    {
        $query = $this->subject->subjectFormatSubject()
            ->where('id', $subjectFormatSubjectId);

        return $query->update($data);
    }

    public function checkSubjectFormatSubjectIsEditable(int $subjectFormatSubjectId)
    {
        if ($this->subject->is_aptitude) {
            return false;
        }

        $subjectFormatSubject = $this->subject->subjectFormatSubject()
            ->where('id', $subjectFormatSubjectId)
            ->first(['is_editable']);

        if ($subjectFormatSubject) {
            return (bool)$subjectFormatSubject->is_editable;
        }

        return true;
    }

    public function createResourceSubjectFormatSubject(
        int $subjectFormatSubjectId,
        array $data
    ): ?ResourceSubjectFormatSubject {
        $subjectFormatSubject = $this->subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);

        if ($subjectFormatSubject) {
            return $subjectFormatSubject->resourceSubjectFormatSubject()
                ->create($data);
        }
        return null;
    }

    public function updateResourceSubjectFormatSubject(
        int $subjectFormatSubjectId,
        $resourceId,
        array $data
    ): ?ResourceSubjectFormatSubject {
        //        $subjectFormatSubject = $this->subject->subjectFormatSubject()
        //            ->find($subjectFormatSubjectId);
        //        if ($subjectFormatSubject) {
        //            $resourceSubjectFormatSubject = $subjectFormatSubject->resourceSubjectFormatSubject()
        //                ->where('id', $resourceId);
        //
        //            $resourceSubjectFormatSubject->update($data);
        //            return $subjectFormatSubject->resourceSubjectFormatSubject()
        //                ->find($resourceId);
        //        }

        ResourceSubjectFormatSubject::where('id', $resourceId)->update($data);
        return ResourceSubjectFormatSubject::find($resourceId);
        return null;
    }

    public function checkResourceSubjectFormatSubjectIsEditable(int $subjectFormatSubjectId, $resourceId): bool
    {
        $subjectFormatSubject = $this->subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            $resourceSubjectFormatSubject = $subjectFormatSubject->resourceSubjectFormatSubject()
                ->where('id', $resourceId)
                ->first(['is_editable']);
            if ($resourceSubjectFormatSubject) {
                return (bool)$resourceSubjectFormatSubject->is_editable;
            }
        }
        return true;
    }

    public function paginateWhereSME($smeId, $filters = [])
    {
        return $this->applyFilters($this->subject, $filters)
            ->where('sme_id', $smeId)
            ->jsonPaginate();
    }

    public function getResourceSubjectFormatSubjectPluckedIds($subjectFormatSubjectId): array
    {
        $subjectFormatSubject = $this->subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            return $subjectFormatSubject->resourceSubjectFormatSubject()->pluck('id')->toArray() ?? [];
        }
        return [];
    }

    public function deleteResourceSubjectFormatSubject(
        $subjectFormatSubjectId,
        $resourcesIds,
        $withIsEditable = false
    ): bool {
        $subjectFormatSubject = $this->subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            $query = $subjectFormatSubject
                ->resourceSubjectFormatSubject()
                ->whereIn('id', $resourcesIds);

            if ($withIsEditable) {
                $query = $query->where('is_editable', 1);
            }

            return $query->delete();
        }
        return false;
    }

    public function deleteResourceSubjectFormatSubjectDirect(
        $resourcesIds,
        $withIsEditable = false
    ): bool {
        $query = ResourceSubjectFormatSubject::whereIn('id', $resourcesIds);

        if ($withIsEditable) {
            $query = $query->where('is_editable', 1);
        }
        return $query->delete();
    }

    public function deleteAllSubjectFormatSubjects($taskSafe = true)
    {
        $subjectFormatSubject = $this->subject->subjectFormatSubject();
        //        if ($taskSafe) {
        //            $subjectFormatSubject->where('resourceSubjectFormatSubject', function ($query) {
        //                return $query->doesntHave('task');
        //            });
        //        }
        $subjectFormatSubject->delete();
    }

    public function generateTask(int $subjectFormatSubjectId, int $resourceSubjectFormatSubjectId, array $data): ?Task
    {
        $subjectFormatSubject = $this->subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);

        if ($subjectFormatSubject) {
            $resourceSubjectFormatSubject = $subjectFormatSubject->resourceSubjectFormatSubject()
                ->find($resourceSubjectFormatSubjectId);

            if ($resourceSubjectFormatSubject) {
                $data['subject_id'] = $this->subject->id;
                $data['is_active'] = true;

                $task = $resourceSubjectFormatSubject->task()->create($data);
                if ($task) {
                    $resourceSubjectFormatSubject->is_editable = 0;
                    $resourceSubjectFormatSubject->save();
                }
                return $task;
            }
        }
        return null;
    }

    public function checkIfResourceHasTask(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        if ($resourceSubjectFormatSubject->has('task')) {
            return true;
        }
        return false;
    }

    public function getContentAuthors()
    {
        return $this->subject->contentAuthors()->get();
    }

    public function getInstructors()
    {
        return $this->subject->instructors()->get();
    }

    public function getSme()
    {
        return $this->subject->sme;
    }

    public function getSubjectTasks(array $filters = [])
    {
        return $this->applyFilters($this->subject->task(), $filters)
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    // web, Admin Dashboard

    public function getSubjectTasksForSMEPaginated(array $filters = [])
    {
        return $this->applyFilters($this->subject->task(), $filters)
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    public function getSubjectQuestionReportTasksForSMEPaginated(array $filters = [])
    {
        return $this->applyFilters($this->subject->questionReportTasks(), $filters)
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    // API

    public function getSubjectActiveTasksForContentAuthorPaginated($user, array $filters = [])
    {
        $tasks = $this->applyFilters($this->subject->task(), $filters);

        $tasks = $tasks->where(function ($query) use ($user) {
            $query->where('is_assigned', 0);
            $query->orWhereHas('contentAuthors', function ($contentAuthor) use ($user) {
                $contentAuthor->where('content_author_id', $user->contentAuthor->id);
            });
        });

        return $tasks
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    public function getSubjectActiveQuestionReportTasksForContentAuthorPaginated($user, array $filters = [])
    {
        $subject = $this->subject->whereHas('contentAuthors', function ($q) use ($user) {
            $q->where('id', $user->id);
        })->first();

        $tasks = $this->applyFilters($subject->questionReportTasks(), $filters);

        $tasks = $tasks->where(function ($query) use ($user) {
            $query->where('is_assigned', 0);
            $query->orWhereHas('contentAuthors', function ($contentAuthor) use ($user) {
                $contentAuthor->where('content_author_id', $user->contentAuthor->id);
            });
        });

        return $tasks->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    // API

    public function getMainSubjectFormatSubject()
    {
        if ($this->subject->subjectFormatSubject()->whereNull('parent_subject_format_id')->exists()) {
            return $this->subject->subjectFormatSubject()->whereNull('parent_subject_format_id')->pluck('id')->toArray();
        }
        return [];
    }

    public function updateTotalPoints()
    {
        $totalPoints = $this->getMainSubjectFormatSubjectTotalPoints();
        return $this->subject->update(['total_points' => $totalPoints]);
    }

    public function getMainSubjectFormatSubjectTotalPoints()
    {
        if ($this->subject->subjectFormatSubject()->whereNull('parent_subject_format_id')->exists()) {
            return $this->subject->subjectFormatSubject()->whereNull('parent_subject_format_id')->sum('total_points');
        }
        return 0;
    }

    public function attachMedia($id, $files)
    {
        foreach ($files as $file) {
            $this->findOrFail($id)->media()->create($file);
        }
        return true;
    }

    /**
     * @param  int          $id
     * @return Subject|null
     */
    public function findOrFail(int $id): ?Subject
    {
        return $this->subject->findOrFail($id);
    }

    public function firstOrFailWithUuid(string $uuid): ?Subject
    {
        return $this->subject->where('our_edu_reference',$uuid)->firstOrFail();
    }

    public function toggleActive()
    {
        $this->subject->is_active = !$this->subject->is_active;

        $this->subject->save();
    }

    public function makeSubjectFormatSubjectNotEditable($ids)
    {
        return $this->subject->subjectFormatSubject()->whereIn('id', $ids)->update(['is_editable' => 0]);
    }

    // API
    public function getLikedSubjectFormatSubjectByUser(int $userId, Subject $subject, int $subjectFormatSubjectId)
    {
        $subjectFormatSubject = $subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            return $subjectFormatSubject->likes()->where('user_id', $userId)->get();
        }
    }

    public function likeSubjectFormatSubjectByUser(int $userId, Subject $subject, int $subjectFormatSubjectId)
    {
        $subjectFormatSubject = $subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            return $subjectFormatSubject->likes()->create([
                'user_id' => $userId,
                'subject_format_subject' => $subjectFormatSubjectId
            ]);
        }
        return [];
    }

    public function unLikeSubjectFormatSubjectByUser(Subject $subject, int $subjectFormatSubjectId)
    {
        $subjectFormatSubject = $subject->subjectFormatSubject()
            ->find($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            return $subjectFormatSubject->likes()->delete();
        }
        return [];
    }

    public function setPracticesNumber($practices)
    {
        foreach ($practices as $practice) {
            $this->subject->where('id', $practice->subject_id)->update([
                'practices_number' => $practice->total
            ]);
        }
    }

    public function getAllSubjectFormatSubjects()
    {
        return $this->subject->subjectFormatSubject;
    }

    public function findOrFailResourceSubject($resourceSubject)
    {
        return ResourceSubjectFormatSubject::findOrFail($resourceSubject);
    }

    public function subjectFormatIncrementPoints($subjectFormatSubjectId, $points)
    {
        return $this->subject->subjectFormatSubject()
            ->where('id', $subjectFormatSubjectId)->increment('total_points', $points);
    }

    public function subjectFormatDecrementPoints($subjectFormatSubjectId, $points)
    {
        return $this->subject->subjectFormatSubject()
            ->where('id', $subjectFormatSubjectId)->decrement('total_points', $points);
    }

    public function resourceSubjectIncrementPoints($resourceId, $points)
    {
        return ResourceSubjectFormatSubject::where('id', $resourceId)->increment('total_points', $points);
    }

    public function pluck()
    {
        return Subject::latest()->pluck('name', 'id');
    }

    public function getPluckSubjectsToArray(): array
    {
        return $this->subject->pluck('name', 'id')->toArray();
    }

    public function paginateFilteredSubjects()
    {
        //don't use subject as static use $this->subject instead
        //and don't use request in repository pass params
        //for pagination use jsonPaginate for api pagination
        //add filter for children subject crteria
        return Subject::latest()
            ->where('is_active', true)
            ->when(request('name'), function ($q) {
                return $q->where('name', 'LIKE', '%' . request('name') . '%');
            })->when(request('educational_system_id'), function ($q) {
                return $q->where('educational_system_id', request('educational_system_id'));
            })->when(request('grade_class_id'), function ($q) {
                return $q->where('grade_class_id', request('grade_class_id'));
            })->when(request('country_id'), function ($q) {
                return $q->where('country_id', request('country_id'));
            })
            ->with('educationalSystem', 'instructors', 'country', 'gradeClass')
            ->paginate(env('PAGE_LIMIT', 20));
    }

    public function pluckSubjectsFilteredToArray(
        $countryId,
        $educationalSystemId,
        $gradeClassId,
        $academicalYearId
    ) {
        return $this->subject
            ->where('is_active', true)
            ->when($countryId, function ($q) use ($countryId) {
                return $q->where('country_id', $countryId);
            })
            ->when($educationalSystemId, function ($q) use ($educationalSystemId) {
                return $q->where('educational_system_id', $educationalSystemId);
            })
            ->when($gradeClassId, function ($q) use ($gradeClassId) {
                return $q->where('grade_class_id', $gradeClassId);
            })
            ->when($academicalYearId, function ($q) use ($academicalYearId) {
                return $q->where('academical_years_id', $academicalYearId);
            })
            ->pluck('name', 'id')->toArray();
    }

    public function pluckSystemSubjects(int $system_id)
    {
        return Subject::where('educational_system_id', $system_id)->pluck('name', 'id')->toArray();
    }

    /**
     * @param  array                $filter
     * @return LengthAwarePaginator
     */
    public function getSubjectWithSuccessRateAndExamCount(array $filter = []): LengthAwarePaginator
    {
        $model = $this->applyFilters(new Subject(), $filter);
        return $model->withCount(['exams' => function ($query) {
            $query->where('type', ExamTypes::EXAM);
        }])
            ->with(['exams' => function ($query) {
                $query->where('type', ExamTypes::EXAM);
            }, 'educationalSystem', 'country', 'gradeClass'])
            ->paginate(env('PAGE_LIMIT', 20));
    }

    /**
     * Export Success Rate Query
     * @param  array           $filter
     * @return Collection|null
     */
    public function getExportSubjectWithSuccessRateAndExamCount(array $filter = []): ?\Illuminate\Database\Eloquent\Collection
    {
        $model = $this->applyFilters(new Subject(), $filter);
        return $model->withCount('exams')
            ->with(['exams' => function ($query) {
                $query->where('type', ExamTypes::EXAM);
            }, 'educationalSystem', 'country', 'gradeClass'])
            ->get();
    }

    /**
     * @return bool
     */
    public function makeSubjectsOutOfDate(): bool
    {
        return $this->subject
            ->whereNotNull('end_date')
            ->where('end_date', '<', date('Y-m-d'))
            ->where('out_of_date', 0)
            ->update(['out_of_date' => 1]);
    }

    public function getAllStudentsProgress($subjectId)
    {
        $orders = SubjectSubscribe::where('subject_id', $subjectId)
            ->orderBy('subject_progress_percentage', 'desc')
            ->get()->pluck('subject_progress_percentage', 'student_id')->toArray();
        // sorting the orders
        return $orders;
    }


    public function filterSubjectsByBranchEducationalSystemAndGradeClass($branchEducationalSystem, $gradeClassId)
    {
        // TODO:: Uncomment educational term and add eduational term to @paginateWhereStudent function
        return $this->subject->where('educational_system_id', $branchEducationalSystem->educational_system_id)
            ->where('country_id', $branchEducationalSystem->branch->schoolAccount->country_id)
            ->where('grade_class_id', $gradeClassId)
            //            ->where('educational_term_id',$branchEducationalSystem->educational_term_id)
            ->where('academical_years_id', $branchEducationalSystem->academic_year_id);
    }

    public function getGradeBranchSubjectsPluck(SchoolAccountBranch $branch, GradeClass $gradeClass)
    {
        $branchEducationalSystemsIDs = $branch->branchEducationalSystem()->pluck("educational_system_id")->toArray();
        $branchEducationalSystemsAcademicYears = $branch->branchEducationalSystem()->pluck("academic_year_id")->toArray();
        $branchEducationalSystemsEdcayionalTerms = $branch->branchEducationalSystem()->pluck("educational_term_id")->toArray();

        $educationalSystem = EducationalSystem::query()->whereIn("id", $branchEducationalSystemsIDs)->pluck("id")->toArray();

        $subjects = Subject::query()
            ->whereIn('educational_system_id', $educationalSystem)
            ->whereIn('academical_years_id', $branchEducationalSystemsAcademicYears)
            ->whereIn('educational_term_id', $branchEducationalSystemsEdcayionalTerms)
            ->where("grade_class_id", "=", $gradeClass->id)
            ->pluck("name", "id");

        return $subjects;
    }

    /**
     * Get all subjects to export
     * @param array $orders
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function dataExport($orders = []): ?\Illuminate\Database\Eloquent\Collection
    {
        $subjects = Subject::query();

        foreach ($orders as $column => $direction) {
            $subjects->orderBy($column, $direction);
        }

        return $subjects->get();
    }

    public function getSubjectWithFinishedVCRSessionsCount()
    {
        return Subject::query()
            ->with("gradeClass", "VCRSchedules.workingDays")
            ->withCount(["VCRSessions" => function (Builder $query) {
                $query->where('vcr_session_type', '!=', VCRSessionEnum::SCHOOL_SESSION);
            }])
            ->whereHas("VCRSessions")
            ->orderByDesc("id")
            ->paginate(env("PAGE_LIMIT", 20));
    }

    public function updateUsingModel($subjectId, $data)
    {
        $subject = Subject::find($subjectId);
        return $subject->update($data);
    }

    public function paginateWhereQudratStudent(
        array $studentData,
              $perPage = null,
              $pageName = 'page',
              $page = null
    ): LengthAwarePaginator {
        $student = auth()->user()->student;
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        $subjects = $this->subject->where('country_id', $studentData['country_id'])->where(
            'educational_system_id',
            $studentData['educational_system_id']
        )->where(
            'academical_years_id',
            $studentData['academical_years_id']
        )->where('is_top_qudrat',true);
        if (request()->has('subscribed')) {
            if (request()->boolean('subscribed')) {
                $subjects->whereHas('students', function ($q) use ($student) {
                    $q->where('student_id', "=", $student->id);
                });
            } else {
                   $subjects->whereDoesntHave("students", function ($q) use ($student){
                    $q->where('student_id', "=", $student->id);
                });
            }
        }

        return $subjects->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }
}
