<?php

namespace App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase;

use App\OurEdu\Helpers\MailManger;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepository;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Collection;

class GenerateTasksUseCase implements GenerateTasksUseCaseInterface
{
    private $repository;
    private $resourceSubjectFormatSubjectRepository;


    public function __construct(SubjectRepositoryInterface $subjectRepository)
    {
        $this->repository = $subjectRepository;
    }

    public function generateTaskForResource(SubjectRepository $subjectRepository, ResourceSubjectFormatSubject $resourceObj)
    {
        if ($resourceObj->task) {
            return $resourceObj->task;
        }

        $dueDate = null;
        $acceptCriteria = $resourceObj->accept_criteria;
        $acceptCriteria = json_decode($acceptCriteria, true);
        if (is_array($acceptCriteria)) {
            foreach ($acceptCriteria as $field) {
                if (isset($acceptCriteria['due_date'])) {
                    $dueDate = intval($acceptCriteria['due_date']);
                }
            }
        }
        $title = $resourceObj->resource->title ?? '';

        $title = __('subject.Created task ') . $title;


        $taskData = [
            'title' => $title,
            'due_date' => $dueDate,
            'is_active' => 1,
            'is_assigned' => 0,
            'is_expired' => 0,
            'resource_subject_format_subject_id' => $resourceObj->id,
            'subject_format_subject_id' => $resourceObj->subject_format_subject_id,

        ];
        $task = $subjectRepository->generateTask($resourceObj->subject_format_subject_id, $resourceObj->id, $taskData);

        $this->makeSubjectFormatSubjectNotEditable($subjectRepository, $resourceObj->subject_format_subject_id);
        $this->sendEmailToContentAuthorsAndInstructor($subjectRepository, $task->load('resourceSubjectFormatSubject.resource'), $title);
        return $task;
    }

    public function sendEmailToContentAuthorsAndInstructor(SubjectRepository $subjectRepository, $task, $title)
    {
        $subject = __('subject.Created task ' . $title);
        $contentAuthors = $subjectRepository->getContentAuthors()->pluck('email')->toArray();
        $instructors = $subjectRepository->getInstructors()->pluck('email')->toArray();

        $data = [
            'task' => $task
        ];

        $newMail = new MailManger();

        $contentAuthorsEmailData = [
            'user_type' => UserEnums::CONTENT_AUTHOR_TYPE,
            'data' => $data,
            'subject' => $subject,
            'emails' => $contentAuthors,
            'view' => 'NewTaskGenerated',
        ];
        $newMail->prepareMail($contentAuthorsEmailData);

        $contentAuthorsEmailData = [
            'user_type' => UserEnums::INSTRUCTOR_TYPE,
            'data' => $data,
            'subject' => $subject,
            'emails' => $instructors,
            'view' => 'NewTaskGenerated',
        ];
        $newMail->prepareMail($contentAuthorsEmailData);


        $newMail->handle();
    }


    public function makeSubjectFormatSubjectNotEditable(SubjectRepository $subjectRepository, $subjectFormatSubjectId)
    {
        $allParentIds = $this->getAllParentSubjectFormatSubject($subjectRepository, $subjectFormatSubjectId);
        $allParentIds[] = $subjectFormatSubjectId;

        $subjectRepository->makeSubjectFormatSubjectNotEditable($allParentIds);
    }

    public function getAllParentSubjectFormatSubject(SubjectRepository $subjectRepository, $subjectFormatSubjectId, $allParentsArray = [])
    {
        $subjectFormatSubject = $subjectRepository->getParentSubjectFormatSubject($subjectFormatSubjectId);
        if ($subjectFormatSubject) {
            $subjectFormatSubjectId = $subjectFormatSubject->id;

            $allParentsArray = array_merge($allParentsArray, $this->getAllParentSubjectFormatSubject($subjectRepository, $subjectFormatSubjectId, [$subjectFormatSubjectId]));
        }
        return $allParentsArray;
    }

    public function generateBulkTasksForResources(array $resourcesIds)
    {
        $tasks = new Collection();
        //TODO::to use dependency injection (used this way to avoid conflicts in delivery night :D)
        $this->resourceSubjectFormatSubjectRepository = new ResourceSubjectFormatSubjectRepository(new ResourceSubjectFormatSubject());
        foreach ($resourcesIds as $resourcesId) {
            $resource = $this->resourceSubjectFormatSubjectRepository->findOrFail($resourcesId);
            if (isset($resource->subjectFormatSubject) && isset($resource->subjectFormatSubject->subject)) {
                $subject = $resource->subjectFormatSubject->subject;
                $task = $this->generateTaskForResource(new SubjectRepository($subject), $resource);
                $tasks->push($task);
            }

        }
        return $tasks;
    }

    public function generateBulkTasksForResourcesForSubject(int $subjectId)
    {
        $tasks = new Collection();
        //TODO::to use dependency injection (used this way to avoid conflicts in delivery night :D)
        $this->resourceSubjectFormatSubjectRepository = new ResourceSubjectFormatSubjectRepository(new ResourceSubjectFormatSubject());
        $resourcesIds = $this->resourceSubjectFormatSubjectRepository->getResourcesIdsForBySubjectId($subjectId);
        foreach ($resourcesIds as $resourcesId) {
            $resource = $this->resourceSubjectFormatSubjectRepository->findOrFail($resourcesId);
            if (isset($resource->subjectFormatSubject) && isset($resource->subjectFormatSubject->subject)) {

                $subject = $resource->subjectFormatSubject->subject;
                $task = $this->generateTaskForResource(new SubjectRepository($subject), $resource);
                $tasks->push($task);
            }

        }
        return $tasks;
    }

}
