<?php

namespace App\OurEdu\Subjects\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Collection;
use function GuzzleHttp\Promise\all;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\Subjects\Models\SubModels\ContentAuthorTask;

class TaskRepository implements TaskRepositoryInterface
{
    private $task;
    private $user;
    use Filterable;

    public function __construct(Task $task, User $user = null)
    {
        $this->task = $task;
        $this->user = $user ?? new User();
    }

    /**
     * @param  null                 $perPage
     * @param  string               $pageName
     * @param  null                 $page
     * @return LengthAwarePaginator
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        $this->task = $this->applyFilters(new Task(), $filters);

        return $this->task->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->task->with('subject')->latest()->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param  int       $id
     * @return task|null
     */
    public function findOrFail(int $id): ?Task
    {
        return $this->task->findOrFail($id);
    }

    public function getAssignedAndNotExpiredTasks()
    {
        return $this->task->with('contentAuthors')
            ->whereHas('contentAuthors', function ($q) {
                $q->where('content_author_task.deleted_at', null);
            })
            ->where('is_expired', 0)
            ->where('is_assigned', 1)
            ->where('is_paused', false)
            ->get();
    }

    public function makeTasksExpired(array $tasksIds)
    {
        return $this->task->whereIn('id', $tasksIds)->update([
            'is_expired' => 1,
            'is_active' => 0
        ]);
    }

    public function getAllSMETasksPaginated($sme, $filters = [])
    {
        $result = $this->applyFilters($this->task, $filters)
            ->whereHas('subject', function ($q) use ($sme) {
                $q->where('sme_id', $sme->id);
            })
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();

        return $result;
    }

    public function getAllContentAuthorActiveTasksPaginated($user, $filters = [])
    {
        // getting the content author id from the actual user object
        $contentAuthorId = $user->id;


        $tasks = $this->applyFilters($this->task, $filters);

        $tasks = $tasks->where(function($query) use ($user) {
            $query->where('is_assigned', 0);
            $query->orWhereHas('contentAuthors' , function ($contentAuthor) use ($user){
                $contentAuthor->where('content_author_id' , $user->contentAuthor->id);
            });
        });
        // returning all tasks that is not assigned to any content author
        return $tasks->with('contentAuthors', 'subject')
            ->whereHas('subject', function ($q) use ($contentAuthorId) {
                $q->where('is_active', 1);
                $q->whereHas('contentAuthors', function ($q) use ($contentAuthorId) {
                    $q->where('subject_content_author.user_id', $contentAuthorId);
                });
            })->distinct()
            ->where('is_expired', 0)
            ->where('is_paused', false)
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }

    public function getSubjectFormatSubjectByTask()
    {
        return $this->task->subjectFormatSubject;
    }

    /**
     * @param $array
     * @return mixed
     */
    public function assignTasksToContentAuthor($array)
    {
        return ContentAuthorTask::insert($array);
    }

    public function unAssignTasksFromContentAuthor($contentAuthorID ,array $taskIds)
    {
        return ContentAuthorTask::where('content_author_id' , $contentAuthorID)->whereIn('task_id' , $taskIds)->forcedelete();
    }

    public function getTaskIn($tasksIds)
    {
        return $this->task->whereIn('id', $tasksIds)->get();
    }

    public function makeTasksAssignedIn(array $tasksIds)
    {
        $update = [];
        foreach ($tasksIds as $taskId) {
            $task = $this->task->find($taskId);
            $update[] = $task->update([
                'is_assigned' => 1,
                'pulled_at' => now()
            ]);
        }
        if (in_array(false, $update)) {
            return false;
        }
        return true;
    }
    public function makeTasksUnAssigned(array $tasksIds)
    {
        $update = [];
        foreach ($tasksIds as $taskId) {
            $task = $this->task->find($taskId);
            $update[] = $task->update([
                'is_assigned' => 0,
                'pulled_at' => now()
            ]);
        }
        if (in_array(false, $update)) {
            return false;
        }
        return true;
    }

    public function update($data = [])
    {
        return $this->task->update($data);
    }

    public function getSmeTasksPerformance($sme)
    {
        $smeSubjects = $sme->managedSubjects()->pluck('id');

        if ($smeSubjects->count()) {
            $this->subjects = $smeSubjects;

            $contentAuthors = ContentAuthor::with(['user', 'tasks' => function ($query) {
                $query->whereIn('subject_id', $this->subjects);
            }])->paginate();

            $contentAuthors->getCollection()->each(function ($author) {
                $author->tasks_count = $author->tasks->count();
                $author->done_tasks_count = $author->tasks->where('is_done', true)->count();
                $author->expired_tasks_count = $author->tasks->where('is_expired', true)->count();
                $author->in_progress_tasks_count = $author->tasks->where('is_expired', false)->where('is_done', false)->count();
            });

            return $contentAuthors;
        }

        return [];
    }

    public function contentAuthorTask()
    {
        $authors = $this->user->where('type', UserEnums::CONTENT_AUTHOR_TYPE)->paginate();
        $authors->each(function ($author, $key) {
            if ($author->contentAuthor) {
                $author->tasksCount = ContentAuthorTask::where('content_author_id', $author->contentAuthor->id)->count();

                $author->expiredTasks = ContentAuthorTask::where('content_author_id', $author->contentAuthor->id)->whereHas('task', function ($task) {
                    $task->where('is_expired', 1);
                })->count();

                $author->notExpiredTasks = $author->tasksCount - $author->expiredTasks;

                $author->doneTasks = ContentAuthorTask::where('content_author_id', $author->contentAuthor->id)->whereHas('task', function ($task) {
                    $task->where('is_done', 1);
                })->count();
            }
        });

        return $authors;
    }

    public function contentAuthorTaskDetails($contentAuthor)
    {
        $author = $this->user->where('type', UserEnums::CONTENT_AUTHOR_TYPE)->where('id', $contentAuthor)->firstOrFail();

        $tasks = new Collection();
        if ($author->contentAuthor) {
            $tasks = $this->task
                ->latest()->whereIn('id', ContentAuthorTask::where('content_author_id', $author->contentAuthor->id)->pluck('task_id')->toArray())
                ->with('resourceSubjectFormatSubject', 'subject', 'subjectFormatSubject')
                ->paginate(5);
        }

        return ['tasks' => $tasks, 'contentAuthor' => $author];
    }

    public function getSubjectTasksPaginated($subject, $filters = [])
    {
        return $this->applyFilters($this->task, $filters)
            ->where('subject_id', $subject->id)
            ->orderBy('due_date', 'ASC')
            ->jsonPaginate();
    }
}
