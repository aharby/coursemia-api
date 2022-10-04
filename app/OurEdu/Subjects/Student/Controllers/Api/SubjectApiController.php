<?php

namespace App\OurEdu\Subjects\Student\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;
use App\OurEdu\Subjects\Student\Transformers\SectionTransformer;
use App\OurEdu\Subjects\Student\Transformers\SubjectMediaTransformer;
use App\OurEdu\Subjects\Student\Transformers\SubjectTransformer;
use App\OurEdu\Subjects\UseCases\NotifyParentsAboutSubjectProgressUseCase\NotifyParentsAboutSubjectProgressUseCaseInterface;
use App\OurEdu\Subjects\UseCases\SubscribeUseCase\SubscribeUseCaseInterface;
use App\OurEdu\Subjects\UseCases\UpdateProgressUseCase\UpdateProgressUseCaseInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubjectApiController extends BaseApiController
{
    private $repository;
    private $studentRepository;
    private $subscribeUseCase;
    private $updateProgressUseCase;
    private $notifyParentsAboutSubjectProgressUseCase;
    protected $subjectFormatSubjectRepo;

    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        StudentRepositoryInterface $studentRepository,
        SubscribeUseCaseInterface $subscribeUseCase,
        UpdateProgressUseCaseInterface $updateProgressUseCase,
        NotifyParentsAboutSubjectProgressUseCaseInterface $notifyParentsAboutSubjectProgressUseCase,
        SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepo
    ) {
        $this->repository = $subjectRepository;
        $this->studentRepository = $studentRepository;
        $this->subscribeUseCase = $subscribeUseCase;
        $this->updateProgressUseCase = $updateProgressUseCase;
        $this->notifyParentsAboutSubjectProgressUseCase = $notifyParentsAboutSubjectProgressUseCase;
        // $this->middleware(AvailableSubjectsMiddleware::class)->only(['postSubscribe']);
        $this->middleware('type:student')->only('getIndex');
        $this->user = Auth::guard('api')->user();
        $this->subjectFormatSubjectRepo = $subjectFormatSubjectRepo;
    }

    // to view the available subjects to subscribe
    public function getIndex(BaseApiRequest $d)
    {
        $student = auth()->user()->student;
        $studentUser = $student->user;
        $studentData = [
            'class_id' => $student->class_id,
            'educational_system_id' => $student->educational_system_id,
            'academical_years_id' => $student->academical_year_id,
            'country_id' => auth()->user()->country_id
        ];
        $data = $this->repository->paginateWhereStudent($studentData);

        $include = 'actions';
        return $this->transformDataModInclude($data, $include, new ListSubjectsTransformer([], $studentUser), ResourceTypesEnums::SUBJECT);
    }

    public function postSubscribe(BaseApiRequest $request, $subjectId)
    {
        try {
            $studentId = auth()->user()->student->id;
            $subscribe = $this->subscribeUseCase->subscribeSubject($subjectId, $studentId);
            if ($subscribe['status'] == 200) {
                return response()->json([
                    'meta' => [
                        'message' => trans('app.Subscribed Successfully')
                    ]
                ]);
            } else {
                return formatErrorValidation($subscribe);
            }
        } catch (Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    public function likeUnLikeSubjectFormat($subjectId, $subjectFormatId)
    {
        try {
            $studentId = auth()->user()->id;
            $subject = $this->repository->findOrFail($subjectId);
            $subjectFormatSubject = $subject->subjectFormatSubject()->findOrFail($subjectFormatId);
            $isSubscribed = auth()->user()->student->subjects->find($subjectId);
            //if the student subscribed to this subject
            if (is_null($isSubscribed)) {
                return formatErrorValidation([
                    'status' => 402,
                    'title' => 'Cant like this section',
                    'detail' => trans('subject.you are not subscribe to this subject')
                ], 402);
            }

            $likedSubjectFormat = $this->repository->getLikedSubjectFormatSubjectByUser($studentId, $subject, $subjectFormatId);
            //if the student liked this section
            if (!$likedSubjectFormat->isEmpty()) {
                //unlike
                $this->repository->unLikeSubjectFormatSubjectByUser($subject, $subjectFormatId);
                return response()->json([
                        'meta' => [
                            'liked' => 0,
                            'message' => trans('subject.Like removed successfully')
                        ]
                    ]);
            } else {
                $this->repository->likeSubjectFormatSubjectByUser($studentId, $subject, $subjectFormatId);
                return response()->json([
                        'meta' => [
                            'liked' => 1,
                            'message' => trans('subject.Like successfully')
                        ]
                    ]);
            }
        } catch (Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    public function postUpdateProgressUseCase($resourceId)
    {
        try {
            $student = auth()->user()->student;
            $this->updateProgressUseCase->updateProgress($student, $resourceId);
            $resource = $this->repository->findOrFailResourceSubject($resourceId);
            $subject = $resource->subjectFormatSubject->subject;
            $this->notifyParentsAboutSubjectProgressUseCase->notifyParents($subject, $student);
        } catch (Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }


    // to get student's subjects
    public function listSubjectsByParent($studentId)
    {
        try {
            $student = $this->studentRepository->findOrFail($studentId);
            $studentUser = $student->user;
            $studentData = [
                'class_id' => $student->class_id,
                'educational_system_id' => $student->educational_system_id,
                'academical_years_id' => $student->academical_year_id,
                'country_id' => $studentUser->country_id
            ];
            $subjects = $this->repository->paginateWhereStudent($studentData);
            return $this->transformDataModInclude($subjects, 'actions', new ListSubjectsTransformer([], $studentUser), ResourceTypesEnums::SUBJECT);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function viewSubject($subjectId, $studentId = null)
    {
        try {
            $subject = $this->repository->findOrFail($subjectId);

            // parent case: parent viewing a student subject
            if (!is_null($studentId)) {
                $student = $this->studentRepository->findOrFail($studentId);
                return $this->transformDataModInclude($subject,
                    'actions,subjectMediaTypes,subjectFormatSubjects',
                    new SubjectTransformer([], $student->user), ResourceTypesEnums::SUBJECT);
            }

            // student case: student views a subject
            return $this->transformDataModInclude($subject,
                'actions,subjectMediaTypes,subjectFormatSubjects',
                new SubjectTransformer([] , auth()->user()), ResourceTypesEnums::SUBJECT);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function viewSubjectMedia($subjectId)
    {
        $subject = $this->repository->findOrFail($subjectId);
        $media = $subject->media()->when(request('type'), function ($query) {
            $query->whereIn('extension', MediaEnums::getTypeExtensions(request('type')));
        })->get();

        return $this->transformDataModInclude($media, '', new SubjectMediaTransformer(), ResourceTypesEnums::SUBJECT_MEDIA);
    }

    public function viewSubjectSections($subjectId)
    {
        $subject = $this->repository->findOrFail($subjectId);
        $params['view_subject_sections'] = true;
        return $this->transformDataModInclude($subject, 'sections.actions', new SubjectTransformer($params), ResourceTypesEnums::SUBJECT);
    }

    public function viewSectionChildSections($sectionId)
    {
        $section = $this->subjectFormatSubjectRepo->findOrFail($sectionId);

        return $this->transformDataModInclude($section, 'sections.actions,parent', new SectionTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }

    public function getQudratIndex()
    {
        $student = auth()->user()->student;
        $studentUser = $student->user;
        $studentData = [
            'country_id' => auth()->user()->country_id,
            'educational_system_id' => $student->educational_system_id,
            'academical_years_id' => $student->academical_year_id,
        ];
        $data = $this->repository->paginateWhereQudratStudent($studentData);

        $include = 'actions';
        return $this->transformDataModInclude($data, $include, new ListSubjectsTransformer([], $studentUser), ResourceTypesEnums::SUBJECT);
    }

    public function listQudratSubjectsByParent($studentId)
    {
        try {
            $student = $this->studentRepository->findOrFail($studentId);
            $studentUser = $student->user;
            $studentData = [
                'country_id' => auth()->user()->country_id,
                'educational_system_id' => $student->educational_system_id,
                'academical_years_id' => $student->academical_year_id,
                ];
            $subjects = $this->repository->paginateWhereQudratStudent($studentData);
            return $this->transformDataModInclude($subjects, 'actions', new ListSubjectsTransformer([], $studentUser), ResourceTypesEnums::SUBJECT);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }


}
