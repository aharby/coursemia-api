<?php

namespace App\OurEdu\Subjects\SME\Controllers\Api;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Subjects\SME\Jobs\CloneSubject;
use Illuminate\Validation\ValidationException;
use App\OurEdu\Subjects\Enums\SectionTypesEnum;
use App\OurEdu\Subjects\Events\SubjectModified;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Events\SubjectPausedEvent;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\Subjects\Events\SubjectResumedEvent;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\OurEdu\Subjects\SME\Requests\CloneSubjectRequest;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;
use App\OurEdu\Subjects\SME\Middleware\Api\SubjectPolicyMiddleware;
use App\OurEdu\Subjects\SME\Requests\UpdateSubjectStructuralRequest;
use App\OurEdu\Subjects\SME\Transformers\ClonedSubjectTransformers\ClonedSubjectTransformer;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;

class SubjectApiController extends BaseApiController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countryRepository;
    private $educationalSystemRepository;
    private $updateSubjectStructuralUseCase;
    private $filters = [];

    public function __construct(
        SubjectRepository $subjectRepository,
        ParserInterface $parserInterface,
        UpdateSubjectStructuralUseCaseInterface $updateSubjectStructuralUseCase
    ) {
        $this->middleware(SubjectPolicyMiddleware::class)->except(['getIndex','getSubject']);

        $this->module = 'subjects';
        $this->repository = $subjectRepository;
        $this->setFilters();

        $this->title = trans('subjects.Subject');
        $this->parent = ParentEnum::SME;
        $this->parserInterface = $parserInterface;
        $this->updateSubjectStructuralUseCase = $updateSubjectStructuralUseCase;
    }

    public function getIndex(BaseApiRequest $d)
    {
        $userId = auth()->user()->id;

        $data = $this->repository->paginateWhereSME($userId, $this->filters);

        $include = 'actions';
        $param = [
            'subjectListAction' => true
        ];
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude($data, $include, new SubjectTransformer($param), ResourceTypesEnums::SUBJECT, $meta);
    }

    public function getSubject($id)
    {
        $data = $this->repository->findOrFail($id);
        $include = 'subjectFormatSubjects,actions';

        $sectionsTypes = SectionTypesEnum::getTypes();
        $sectionsTypes = formatFilter($sectionsTypes);
        $meta = [
            'sections_types' => $sectionsTypes
        ];

        $param = ['method' => 'GET'];
        return $this->transformDataModIncludeItem(
            $data,
            $include,
            new SubjectTransformer($param),
            ResourceTypesEnums::SUBJECT,
            $meta
        );
    }

    public function getSubjectWithMinimalData($id)
    {
        $data = $this->repository->findOrFail($id);
        $include = 'subjectFormatSubjects,actions';

        $sectionsTypes = SectionTypesEnum::getTypes();
        $sectionsTypes = formatFilter($sectionsTypes);
        $meta = [
            'sections_types' => $sectionsTypes
        ];

        $param = [
            'method' => 'GET',
            'minimal_data' => true,
        ];
        return $this->transformDataModIncludeItem(
            $data,
            $include,
            new SubjectTransformer($param),
            ResourceTypesEnums::SUBJECT,
            $meta
        );
    }
    public function updateSubjectWithMinimalData($id)
    {
        $data = $this->repository->findOrFail($id);
        $include = 'subjectFormatSubjects,actions';

        $sectionsTypes = SectionTypesEnum::getTypes();
        $sectionsTypes = formatFilter($sectionsTypes);
        $meta = [
            'sections_types' => $sectionsTypes
        ];

        $param = [
            'method' => 'GET',
            'minimal_data' => true,
        ];
        return $this->transformDataModIncludeItem(
            $data,
            $include,
            new SubjectTransformer($param),
            ResourceTypesEnums::SUBJECT,
            $meta
        );






        $data = $request->getContent();
        $isGenerate = $request->is_generate ?? false;
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $subject = $this->repository->findOrFail($id);
        $isAptitude = $subject->is_aptitude;



        if ($data->attachMedia) {
            foreach ($data->attachMedia as $media) {
                moveGarbageMedia($media->getId(), $subject->media(), 'subject');
            }
        }

        if ($data->detachMedia) {
            $subjectRepo = new SubjectRepository($subject);
            foreach ($data->detachMedia as $media) {
                deleteMedia($media->getId(), $subject->media(), 'subject');
            }
        }
        $subjectFormatSubjects =
            $isAptitude
                ? $subject->load('subjectformatsubject')->subjectformatsubject
                : $data->subject_format_subjects ?? [];
        $removedSections = !$isAptitude ? $data->removed_sections : '';
        $removedResources = !$isAptitude ? $data->removed_resources : '';

        try {
            DB::beginTransaction();

            $subjectRepo = new SubjectRepository($subject);

            $subjectRepo->update([
                'section_type' => $data->section_type,
                'subject_library_text' => $data->subject_library_text,
                'is_active' => (int) ($data->is_active ?? 0),
            ]);
            //removed_sections
            //removed_resources
            $this->updateSubjectStructuralUseCase->updateNestedStructural(
                $subjectFormatSubjects,
                $id,
                null,
                $isGenerate,
                $isAptitude,
                $removedSections,
                $removedResources
            );
            DB::commit();

            $subject = $this->repository->findOrFail($id);

            SubjectModified::dispatch([], $subject->toArray(), 'Structure updated');

            $include = 'subjectFormatSubjects,actions';

            $sectionsTypes = SectionTypesEnum::getTypes();
            $sectionsTypes = formatFilter($sectionsTypes);
            $meta = [
                'sections_types' => $sectionsTypes,
                //                'filters' => formatFiltersForApi($this->filters)
            ];
            if ($isGenerate) {
                $meta['message'] = trans('subject.Tasks Generated Successfully');
            } else {
                $meta['message'] = trans('subject.Saved Successfully');
            }

            $param = [
                'method' => 'GET',
            ];

            return $this->transformDataModIncludeItem(
                $subject,
                $include,
                new SubjectTransformer($param),
                ResourceTypesEnums::SUBJECT,
                $meta
            );
        } catch (ValidationException $exception) {
            $errorArray = [];
            $errors = $exception->errors();
            foreach ($errors as $name => $error) {
                $errorArray[] = [
                    'status' => 422,
                    'title' => $name,
                    'detail' => $error[0],
                ];
            }
            throw new HttpResponseException(response()->json(['errors' => $errorArray], 422));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
    public function updatePublicLibrary(Request $request,$id)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $subject = $this->repository->findOrFail($id);

        try {
            DB::beginTransaction();

            $subjectRepo = new SubjectRepository($subject);
            if ($data->attachMedia) {
                foreach ($data->attachMedia as $media) {
                    moveGarbageMedia($media->getId(), $subject->media(), 'subject');
                }
            }

            if ($data->detachMedia) {
                $subjectRepo = new SubjectRepository($subject);
                foreach ($data->detachMedia as $media) {
                    deleteMedia($media->getId(), $subject->media(), 'subject');
                }
            }
            $subjectRepo->update([
                'subject_library_text' => $data->subject_library_text,
            ]);


            DB::commit();

            $subject = $this->repository->findOrFail($id);

            SubjectModified::dispatch([], $subject->toArray(), 'Structure updated');


            $meta['message'] = trans('subject.Saved Successfully');

            $param = [
                'method' => 'POST',
                'minimal_data' => true,
            ];
            return $this->transformDataModIncludeItem(
                $subject,
                '',
                new SubjectTransformer($param),
                ResourceTypesEnums::SUBJECT,
                $meta
            );
        } catch (ValidationException $exception) {
            $errorArray = [];
            $errors = $exception->errors();
            foreach ($errors as $name => $error) {
                $errorArray[] = [
                    'status' => 422,
                    'title' => $name,
                    'detail' => $error[0],
                ];
            }
            throw new HttpResponseException(response()->json(['errors' => $errorArray], 422));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /*
        This difference between getSubjectDetails and getSubject lies in :-
            getSubjectDetails made for subject edit resource details
            getSubject made for edit subject structure
    */
    public function getSubjectDetails($id)
    {
        $data = $this->repository->findOrFail($id);
        $include = 'subjectFormatSubjects,actions,subjectMedia';

        $sectionsTypes = SectionTypesEnum::getTypes();
        $sectionsTypes = formatFilter($sectionsTypes);
        $meta = [
            'sections_types' => $sectionsTypes
        ];

        $param = [
            'method' => 'GET',
        ];
        return $this->transformDataModIncludeItem(
            $data,
            $include,
            new \App\OurEdu\Subjects\SME\Transformers\EditResources\SubjectTransformer($param),
            ResourceTypesEnums::SUBJECT,
            $meta
        );
    }

    public function updateSubjectStructural(UpdateSubjectStructuralRequest $request, $id)
    {
        $data = $request->getContent();
        $isGenerate = $request->is_generate ?? false;
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $subject = $this->repository->findOrFail($id);
        $isAptitude = $subject->is_aptitude;

        if ($isAptitude) {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'cant update subject structure',
                'detail' => trans('subject.Cant Update Subject Structure')
            ], 403);
        }

        if ($data->attachMedia) {
            foreach ($data->attachMedia as $media) {
                moveGarbageMedia($media->getId(), $subject->media(), 'subject');
            }
        }

        if ($data->detachMedia) {
            $subjectRepo = new SubjectRepository($subject);
            foreach ($data->detachMedia as $media) {
                deleteMedia($media->getId(), $subject->media(), 'subject');
            }
        }
        $subjectFormatSubjects =
            $isAptitude
              ? $subject->load('subjectformatsubject')->subjectformatsubject
              : $data->subject_format_subjects ?? [];
        $removedSections = !$isAptitude ? $data->removed_sections : '';
        $removedResources = !$isAptitude ? $data->removed_resources : '';

        try {
            DB::beginTransaction();

            $subjectRepo = new SubjectRepository($subject);

            $subjectRepo->update([
                'section_type' => $data->section_type,
                'subject_library_text' => $data->subject_library_text,
                'is_active' => (int) ($data->is_active ?? 0),
            ]);
            //removed_sections
            //removed_resources
            $this->updateSubjectStructuralUseCase->updateNestedStructural(
                $subjectFormatSubjects,
                $id,
                null,
                $isGenerate,
                $isAptitude,
                $removedSections,
                $removedResources
            );
            DB::commit();

            $subject = $this->repository->findOrFail($id);

            SubjectModified::dispatch([], $subject->toArray(), 'Structure updated');

            $include = 'subjectFormatSubjects,actions';

            $sectionsTypes = SectionTypesEnum::getTypes();
            $sectionsTypes = formatFilter($sectionsTypes);
            $meta = [
                'sections_types' => $sectionsTypes,
                //                'filters' => formatFiltersForApi($this->filters)
            ];
            if ($isGenerate) {
                $meta['message'] = trans('subject.Tasks Generated Successfully');
            } else {
                $meta['message'] = trans('subject.Saved Successfully');
            }

            $param = [
                'method' => 'GET',
            ];

            return $this->transformDataModIncludeItem(
                $subject,
                $include,
                new SubjectTransformer($param),
                ResourceTypesEnums::SUBJECT,
                $meta
            );
        } catch (ValidationException $exception) {
            $errorArray = [];
            $errors = $exception->errors();
            foreach ($errors as $name => $error) {
                $errorArray[] = [
                    'status' => 422,
                    'title' => $name,
                    'detail' => $error[0],
                ];
            }
            throw new HttpResponseException(response()->json(['errors' => $errorArray], 422));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function pauseUnPauseSubject(Request $request, $id)
    {
        $subject = $this->repository->findOrFail($id);

        if ($subject->is_aptitude) {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'cant pause subject',
                'detail' => trans('subject.Cant Pause Subject')
            ], 403);
        }

        $subjectRepo = new SubjectRepository($subject);

        $subjectRepo->toggleActive();

        if ($subject->fresh()->is_active) {
            SubjectModified::dispatch([], $subject->toArray(), 'Subject resumed');
            SubjectResumedEvent::dispatch($subject);
            $meta = [
                'message' => trans('subject.Subject un-paused successfully')
            ];
        } else {
            SubjectModified::dispatch([], $subject->toArray(), 'Subject paused');
            SubjectPausedEvent::dispatch($subject);
            $meta = [
                'message' => trans('subject.Subject paused successfully')
            ];
        }

//        $include = 'subjectFormatSubjects,actions';
//        $sectionsTypes = SectionTypesEnum::getTypes();
//        $sectionsTypes = formatFilter($sectionsTypes);
//        $meta = [
//            'sections_types' => $sectionsTypes,
        ////            'filters' => formatFiltersForApi($this->filters)
//        ];
//
//        return $this->transformDataModIncludeItem(
//            $subject,
//            $include,
//            new SubjectTransformer(),
//            ResourceTypesEnums::SUBJECT,
//            $meta
//        );
        return response()->json(['meta' => $meta], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return array|JsonResponse
     */
    public function getQuestionsReportList(Request $request, $id)
    {
        $subject = $this->repository->findOrFail($id);

        $subjectRepo = new SubjectRepository($subject);

        $sectionsTypes = SectionTypesEnum::getTypes();
        $sectionsTypes = formatFilter($sectionsTypes);
        $meta = [
            'sections_types' => $sectionsTypes,
            //            'filters' => formatFiltersForApi($this->filters)
        ];

        return $this->transformDataModIncludeItem(
            $subject,
            '',
            new SubjectTransformer(),
            ResourceTypesEnums::SUBJECT,
            $meta
        );
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);
        if ($this->repository->delete($row)) {
            flash()->success(trans('app.Deleted Successfully'));
            SubjectModified::dispatch([], $row->toArray(), 'Subject deleted');

            return redirect()->route('admin.gradeClasses.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getCloneSubject($id)
    {
        $clonedSubject = $this->repository->findOrFail($id);
        $include = 'subjectFormatSubjects,actions,subjectMedia';
        return $this->transformDataModIncludeItem(
            $clonedSubject,
            $include,
            new ClonedSubjectTransformer(),
            ResourceTypesEnums::SUBJECT
        );
    }

    /**
     * @param $id
     * @param  CloneSubjectRequest $request
     * @return array|JsonResponse
     */
    public function postCloneSubject(CloneSubjectRequest $request, $id)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $subject = $this->repository->findOrFail($id);
        $this->dispatch(new CloneSubject($subject, $data));

        return response()->json(
            [
                'meta' => [
                    'message' => trans('subject.Cloning Subject')
                ]
            ]
        );
    }

    protected function setFilters()
    {
        $options = Option::whereIn('type', [
            OptionsTypes::ACADEMIC_YEAR,
            OptionsTypes::EDUCATIONAL_TERM,
        ])->get();

        $this->filters[] = [
            'name' => 'country_id',
            'type' => 'select',
            'data' => Country::get()->pluck('name', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('country_id'),
        ];

        $this->filters[] = [
            'name' => 'educational_system_id',
            'type' => 'select',
            'data' => EducationalSystem::get()->pluck('name', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('educational_system_id'),
        ];

        $this->filters[] = [
            'name' => 'grade_class_id',
            'type' => 'select',
            'data' => GradeClass::get()->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('grade_class_id'),
        ];

        $this->filters[] = [
            'name' => 'educational_term_id',
            'type' => 'select',
            'data' => $options->where('type', OptionsTypes::EDUCATIONAL_TERM)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('educational_term_id'),
        ];

        $this->filters[] = [
            'name' => 'academical_years_id',
            'type' => 'select',
            'data' => $options->where('type', OptionsTypes::ACADEMIC_YEAR)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('academical_years_id'),
        ];

        $this->filters[] = [
            'name' => 'is_active',
            'type' => 'select',
            'data' => [
                'no' => trans('app.No'),
                'yes' => trans('app.Yes')
            ],
            'pipes' => 'TrueFalse',
            'trans' => false,
            'value' => request()->get('is_active'),
        ];
    }
}
