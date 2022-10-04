<?php


namespace App\OurEdu\QuestionReport\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\QuestionReport\Models\QuestionReportContentAuthorTask;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionReportTaskRepository implements QuestionReportTaskRepositoryInterface
{
    private $questionReportTask;
    use Filterable;
    public function __construct(QuestionReportTask $questionReportTask)
    {
        $this->questionReportTask = $questionReportTask;
    }

    public function create(array $data): QuestionReportTask
    {
        return $this->questionReportTask->create($data);
    }

    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->questionReportTask->with('subject')->latest()->jsonPaginate(
            $perPage,
            ['*'],
            $pageName,
            $page = null
        );
    }

    /**
     * @param  int  $id
     * @return task|null
     */
    public function findOrFail(int $id): ?QuestionReportTask
    {
        return $this->questionReportTask->findOrFail($id);
    }

    public function getAssignedAndNotExpiredTasks()
    {
        return $this->questionReportTask->with('contentAuthors')
            ->whereHas('contentAuthors', function ($q) {
                $q->where('question_report_content_author_tasks.deleted_at', null);
            })
            ->where('is_expired', 0)
            ->where('is_assigned', 1)
            ->get();
    }

    public function makeTasksExpired(array $tasksIds)
    {
        return $this->questionReportTask->whereIn('id', $tasksIds)->update([
            'is_expired' => 1,
            'is_active' =>  0
        ]);
    }

    public function getAllSMETasksPaginated($sme, array $filters = [])
    {

        // returning all tasks that is not assigned to any content author
        return $this->applyFilters($this->questionReportTask , $filters)->whereHas('subject', function ($q) use ($sme) {
            $q->where('sme_id', $sme->id);
        })->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    public function getAllContentAuthorActiveTasksPaginated(
        $user,
       array $filters = []
    ) {
        // getting the content author id from the actual user object
        $userContentAuthorId = $user->id;

        // returning all tasks that is not assigned to any content author
        return $this->applyFilters($this->questionReportTask , $filters)
            ->with('contentAuthors', 'subject')
            ->whereHas('subject', function ($q) use ($userContentAuthorId) {
                $q->where('is_active', 1);
                $q->whereHas('contentAuthors', function ($q) use ($userContentAuthorId) {
                    $q->where('id', $userContentAuthorId);
                });
            })->distinct()
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    public function getSubjectFormatSubjectByTask()
    {
        return $this->questionReportTask->subjectFormatSubject;
    }

    /**
     * @param $array
     * @return mixed
     */
    public function assignTasksToContentAuthor($array)
    {
        return QuestionReportContentAuthorTask::insert($array);
    }

    public function getTaskIn($tasksIds)
    {
        return $this->questionReportTask->whereIn('id', $tasksIds)->get();
    }

    public function makeTasksAssignedIn(array $tasksIds)
    {
        return $this->questionReportTask->whereIn('id', $tasksIds)->update([
            'is_assigned' => 1,
            'pulled_at' =>  now()
        ]);
    }

    public function markTaskAsDone($task)
    {
        return $task->update([
            'is_done'   =>  1,
            'is_active' =>  0
        ]);
    }
}
