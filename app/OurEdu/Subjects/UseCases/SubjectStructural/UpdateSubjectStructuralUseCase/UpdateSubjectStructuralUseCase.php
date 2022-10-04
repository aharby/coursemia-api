<?php

namespace App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase;

use App\OurEdu\ResourceSubjectFormats\Repository\Flash\FlashRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepository;
use App\OurEdu\LearningResources\Enums\LearningResourcesPointsEnums;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase\GenerateTasksUseCaseInterface;

class UpdateSubjectStructuralUseCase implements UpdateSubjectStructuralUseCaseInterface
{
    private $repository;
    private $generateTasksUseCase;

    public function __construct(SubjectRepositoryInterface $subjectRepository, GenerateTasksUseCaseInterface $generateTasksUseCase)
    {
        $this->repository = $subjectRepository;
        $this->generateTasksUseCase = $generateTasksUseCase;
    }

    /**
     * @param $array
     * @param $subjectId
     * @param  null                $parentSubjectFormatId
     * @param  bool                $isGenerateTask
     * @param  null                $removedSections
     * @param  null                $removedResources
     * @throws ValidationException
     */
    public function updateNestedStructural($array, $subjectId, $parentSubjectFormatId = null, bool $isGenerateTask = false, bool $isAptitudeSubject = false, $removedSections = null, $removedResources = null)
    {
        $subject = $this->repository->findOrFail($subjectId);
        $subjectRepository = new SubjectRepository($subject);

        $this->deleteResourcesDirect($subjectRepository, $removedResources);

        if (count($array) > 0) {
            foreach ($array as $section) {
                $parentSubjectFormatSubjectId =
                    !$isAptitudeSubject
                        ? $this->createOrUpdateSection($subjectRepository, $subjectId, $section, $parentSubjectFormatId)
                        : $section->id;

                $this->NestedStructuralResources($subjectRepository, $section, $parentSubjectFormatSubjectId, $isGenerateTask);

                if (isset($section->subject_format_subjects)) {
                    $this->updateNestedStructural($section->subject_format_subjects, $subjectId, $parentSubjectFormatSubjectId, $isGenerateTask);
                }
            }
        }

        $subjectRepository->updateTotalPoints();
    }

    /**
     * @param $subjectRepository
     * @param $subjectId
     * @param $category
     * @param $parentSubjectFormatId
     * @return mixed
     */
    public function createOrUpdateSection(SubjectRepository $subjectRepository, $subjectId, $category, $parentSubjectFormatId)
    {
        if (Str::contains($category->getId(), 'new')) {
            $parentSubjectFormatSubject =
                $subjectRepository->CreateSubjectFormatSubject([
                    'title' => $category->title,
                    'description' => $category->description,
                    'subject_id' => $subjectId,
                    'is_active' => $category->is_active ?? 0,
                    'parent_subject_format_id' => $parentSubjectFormatId,
                    'list_order_key' => $category->list_order_key ?? 0
                ]);

            $parentSubjectFormatSubjectId = $parentSubjectFormatSubject->id;
            return $parentSubjectFormatSubjectId;
        }

        // delete subject format subject
        $parentSubjectFormatSubjectId = $category->getId();

        if ($subjectRepository->checkSubjectFormatSubjectIsEditable($category->getId())) {
            $subjectData = [
                'title' => $category->title,
                'description' => $category->description,
                'is_active' => $category->is_active ?? 0,
                'parent_subject_format_id' => $parentSubjectFormatId,
                'list_order_key' => $category->list_order_key ?? 0
            ];
        } else {
            $subjectData = [
                'title' => $category->title,
                'description' => $category->description,
                'list_order_key' => $category->list_order_key ?? 0
            ];

            if(!is_null($category->is_active)){
                $subjectData['is_active']=$category->is_active;
            }
        }
        $subjectRepository->updateSubjectFormatSubject($category->getId(), $subjectData);

        return $parentSubjectFormatSubjectId;
    }

    /**
     * @param SubjectRepository $repo
     * @param $section
     * @param $parentSubjectFormatSubjectId
     * @param  bool                $isGenerateTask
     * @throws ValidationException
     */
    public function NestedStructuralResources(SubjectRepository $repo, $section, $parentSubjectFormatSubjectId, bool $isGenerateTask)
    {
        if ($section->resource_subject_format_subjects) {
            foreach ($section->resource_subject_format_subjects as $resource) {
                $this->createOrUpdateResource($repo, $resource, $parentSubjectFormatSubjectId, $isGenerateTask);
            }
        }
    }

    /**
     * @param SubjectRepository $repo
     * @param $resource
     * @param $parentSubjectFormatSubjectId
     * @param  bool                                                                    $isGenerateTask
     * @return \App\OurEdu\Subjects\Models\SubModels\ResourceSubjectFormatSubject|null
     * @throws ValidationException
     */
    public function createOrUpdateResource(SubjectRepository $repo, $resource, $parentSubjectFormatSubjectId, bool $isGenerateTask)
    {
        $this->validate($resource);
        if (Str::contains($resource->getId(), 'new')) {
            $resourceObj = $repo->createResourceSubjectFormatSubject(
                $parentSubjectFormatSubjectId,
                [
                    'resource_id' => $resource->resource_id,
                    'subject_id' =>$repo->subject->id,
                    'resource_slug' => $resource->resource_slug,
                    'subject_format_subject_id' => $parentSubjectFormatSubjectId,
                    'is_active' => $resource->is_active ?? 0,
                    'list_order_key' => $resource->list_order_key ?? 0,
                    'accept_criteria' => json_encode($resource->learningResourceAcceptCriteria)
                ]
            );
            $this->updateProgress($repo, $resourceObj->id, $resourceObj->resource_slug, $parentSubjectFormatSubjectId);
        } else {
            if ($repo->checkResourceSubjectFormatSubjectIsEditable(
                $parentSubjectFormatSubjectId,
                $resource->getId()
            )) {
                $resourceData = [
                    'subject_format_subject_id' => $parentSubjectFormatSubjectId,

                    'is_active' => $resource->is_active ?? 0,
                    'list_order_key' => $resource->list_order_key ?? 0,
                    'accept_criteria' => json_encode($resource->learningResourceAcceptCriteria)
                ];
            } else {
                $resourceData = [
                    'list_order_key' => $resource->list_order_key ?? 0,
                ];
            }

            $resourceObj = $repo->updateResourceSubjectFormatSubject(
                $parentSubjectFormatSubjectId,
                $resource->getId(),
                $resourceData
            );
        }
        if ($resourceObj && ($isGenerateTask == 'true')) {
            $this->generateTasksUseCase->generateTaskForResource($repo, $resourceObj);
        }
        return $resourceObj;
    }

    private function validate($resource)
    {
        $acceptCriteria = LearningResourcesEnums::LearningResources[$resource->resource_slug] ?? [];
        $rules = [];
        if (isset($resource->learningResourceAcceptCriteria)) {
            $acceptCriteriaArray = $resource->learningResourceAcceptCriteria->toArray();
            foreach ($acceptCriteria as $key => $value) {
                if (!isset($acceptCriteriaArray[$key])) {
                    $acceptCriteriaArray[$key] = null;
                }
            }
//            $acceptCriteriaArray = array_merge($acceptCriteriaArray,$acceptCriteria);
            foreach ($acceptCriteriaArray as $keyFiled => $value) {
                if (isset($acceptCriteria[$keyFiled]['validation'])) {
                    $rules[$keyFiled] = $acceptCriteria[$keyFiled]['validation'];
                }
            }
            $validator = Validator::make($acceptCriteriaArray, $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
    }

    /**
     * @param  SubjectFormatSubject $subjectFormatSubject
     * @param  bool                 $onlyNotAssigned
     * @param  Collection           $tasks
     * @return Collection
     */
    public function getSubjectFormatSubjectTasks(SubjectFormatSubject $subjectFormatSubject, bool $onlyNotAssigned = false, ?Collection $tasks = null)
    {
        $tasks = $tasks ?? new Collection();

        $tasks = $tasks->merge($this->getTasks($subjectFormatSubject, $onlyNotAssigned));
        $childSubjectFormatSubject = $subjectFormatSubject->childSubjectFormatSubject;
        foreach ($childSubjectFormatSubject as $singleSubjectFormatSubject) {
            $tasks = $tasks->merge($this->getSubjectFormatSubjectTasks($singleSubjectFormatSubject, $onlyNotAssigned, $tasks));
        }
        return $tasks;
    }

    /**
     * @param $singleSubjectFormatSubject
     * @param $onlyNotAssigned
     * @return array
     */
    public function getTasks($singleSubjectFormatSubject, $onlyNotAssigned)
    {
        $subjectFormatSubjectRepository = new SubjectFormatSubjectRepository($singleSubjectFormatSubject);
        return $subjectFormatSubjectRepository->getSectionTasks($singleSubjectFormatSubject, $onlyNotAssigned);
    }

    public function updateProgress(SubjectRepository $subjectRepo, $resourceId, $resourceSlug, $parentSubjectFormatSubjectId, $increment = true)
    {
        if (!isset(LearningResourcesPointsEnums::getLearningResourcesPointsEnums()[$resourceSlug])) {
            return;
        }

        $parents = $this->generateTasksUseCase->getAllParentSubjectFormatSubject($subjectRepo, $parentSubjectFormatSubjectId);

        $parents[] = $parentSubjectFormatSubjectId;

        $points = LearningResourcesPointsEnums::getLearningResourcesPointsEnums()[$resourceSlug];
        foreach ($parents as $sectionFormatId) {
            if ($increment) {
                $subjectRepo->subjectFormatIncrementPoints($sectionFormatId, $points);
            } else {
                $subjectRepo->subjectFormatDecrementPoints($sectionFormatId, $points);
            }
        }

        if ($resourceId) {
            $subjectRepo->resourceSubjectIncrementPoints($resourceId, $points);
        }
    }

    public function deleteResourcesDirect(SubjectRepository $subjectRepository, $relathionship)
    {
        $deleteIds = [];
        if ((!is_null($relathionship)) && method_exists($relathionship, 'pluck')) {
            $deleteIds = $relathionship->pluck('id')->toArray();
        }
        $subjectRepository->deleteResourceSubjectFormatSubjectDirect($deleteIds, true);
    }

    /**
     * @param SubjectRepository $subjectRepository
     * @param $section
     * @param $parentSubjectFormatSubjectId
     */
    private function deleteSubjectFormatSubjects(SubjectRepository $subjectRepository, $section, $parentSubjectFormatSubjectId)
    {
        $subjectFormatSubjects = $section->subject_format_subjects ?? [];
        $updatedIds = [];
        if (method_exists($subjectFormatSubjects, 'pluck')) {
            $updatedIds = $subjectFormatSubjects->pluck('id')->toArray() ?? [];
        }

        $currentIds = $subjectRepository->getChildrenSubjectFormatSubjectPluckedIds($parentSubjectFormatSubjectId);
        $deleteIds = array_diff($currentIds, $updatedIds);

        $subjectRepository->deleteSubjectFormatSubject($deleteIds, true);
    }

    public function updateParentsProgressOnDelete(SubjectRepository $repo , $section)
    {
        $parents = $this->generateTasksUseCase->getAllParentSubjectFormatSubject($repo, $section->id);

         foreach ($parents as $parent) {
             $repo->subjectFormatDecrementPoints($parent, $section->total_points);
         }

    }

}
