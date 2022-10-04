<?php

namespace App\OurEdu\Subjects\Admin\Controllers;

use Illuminate\Support\Str;
use App\OurEdu\Options\Option;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Helpers\MailManger;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use Illuminate\Database\Eloquent\Builder;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\Subjects\Admin\Exports\SubjectsNamesAndImagesExport;
use App\OurEdu\Subjects\Events\SubjectModified;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Subjects\Events\SubjectPausedEvent;
use App\OurEdu\Subjects\Events\SubjectResumedEvent;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Subjects\Enums\SubjectDirectionsEnum;
use App\OurEdu\Subjects\Admin\Exports\SubjectsExport;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\Admin\Requests\SubjectRequest;
use App\OurEdu\Subjects\Admin\Exports\SuccessRateExport;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\Subjects\Middleware\CheckSubjectUsageMiddleware;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactory;
use App\OurEdu\Subjects\Models\Subject;
use App\Producers\Subject\SubjectCreated;
use App\Producers\Subject\SubjectUpdated;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;

class SubjectController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countryRepository;
    private $educationalSystemRepository;
    private $optionRepository;
    private $gradeClassRepository;
    private $userRepository;
    private $notifierFactory;
    private $filters = [];

    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        CountryRepositoryInterface $countryRepository,
        EducationalSystemRepositoryInterface $educationalSystemRepository,
        OptionRepositoryInterface $optionRepository,
        GradeClassRepositoryInterface $gradeClassRepository,
        UserRepositoryInterface $userRepository,
        NotifierFactory $notifierFactory
    )
    {
        $this->module = 'subjects';
        $this->repository = $subjectRepository;
        $this->countryRepository = $countryRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;
        $this->optionRepository = $optionRepository;
        $this->gradeClassRepository = $gradeClassRepository;
        $this->userRepository = $userRepository;
        $this->notifierFactory = $notifierFactory;

        $this->title = trans('subjects.Subjects');
        $this->parent = ParentEnum::ADMIN;
        $this->middleware(CheckSubjectUsageMiddleware::class)->only('delete');
    }

    public function getIndex()
    {
        $sortBy = request()->input('sortby','created_at');

        $this->setFilters();
        $data['filters'] = $this->filters;
        $count = [
            'exams' => function (Builder $query) {
                $query->where('type', ExamTypes::PRACTICE);
            }
        ];
        $data['rows'] = $this->repository->all($this->filters,$sortBy);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'name',
            'type' => 'input',
            'trans' => false,
            'value' => request()->get('name'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.name'),
                'placeholder' => trans('subject.name'),
            ]
        ];
        $this->filters[] = [
            'name' => 'country_id',
            'type' => 'select',
            'data' => $this->countryRepository->pluck('id', 'name'), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('country_id'),
            'attributes' => [
                'id' => 'country_id',
                'class' => 'form-control',
                'label' => trans('grade_classes.Country'),
                'placeholder' => trans('grade_classes.Country')
            ]
        ];
        $this->filters[] = [
            'name' => 'educational_system_id',
            'type' => 'select',
            'data' => $this->educationalSystemRepository->pluck('id', 'name'), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('educational_system_id'),
            'attributes' => [
                'id' => 'educational_system_id',
                'class' => 'form-control',
                'label' => trans('grade_classes.Educational System'),
                'placeholder' => trans('grade_classes.Educational System')
            ]
        ];
        $this->filters[] = [
            'name' => 'grade_class_id',
            'type' => 'select',
            'data' => $this->gradeClassRepository->pluck('id', 'name'), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('grade_class_id'),
            'attributes' => [
                'id' => 'grade_class_id',
                'class' => 'form-control',
                'label' => trans('grade_classes.Grade Class'),
                'placeholder' => trans('grade_classes.Grade Class')
            ]
        ];

        $this->filters[] = [
            'name' => 'is_top_qudrat',
            'type' => 'select',
            'data' => [
                'true' => trans('subjects.is_top_qudrat'),
                'false' => trans('subjects.is_not_top_qudrat')
            ],
            'pipes' => 'TrueFalse',
            'trans' => false,
            'value' => request()->get('is_top_qudrat'),
            'attributes' => [
                'id' => 'is_top_qudrat',
                'class' => 'form-control',
                'label' => trans('subjects.is_top_qudrat'),
            ]
        ];

    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjects.get.index')];

        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function lookup()
    {
        $data['countries'] = $this->countryRepository->pluck()->toArray();
        $data['educationalSystemRepository'] = $this->educationalSystemRepository->pluck()->toArray();
        $data['gradeClasses'] = $this->gradeClassRepository->pluck();
        $data['directions'] = SubjectDirectionsEnum::getDirections();
        $data['educationalTerms'] = $this->optionRepository->pluckByType(OptionsTypes::EDUCATIONAL_TERM);

        $data['academicalYears'] = $this->optionRepository->pluckByType(OptionsTypes::ACADEMIC_YEAR);
        $data['instructors'] = $this->userRepository->getPluckUserByType(UserEnums::INSTRUCTOR_TYPE);
        $data['contentAuthors'] = $this->userRepository->getPluckUserByType(UserEnums::CONTENT_AUTHOR_TYPE);
        $data['smes'] = $this->userRepository->getPluckUserByType(UserEnums::SME_TYPE);
        return $data;
    }

    public function postCreate(SubjectRequest $request)
    {
        $data = $request->except(['_token', 'instructors', 'content_authors']);


        if (isset($data['image']) && !in_array($data['image']->getClientOriginalExtension(), ['jpeg', 'bmp', 'png', 'jpg'])) {
            flash()->error(trans('app.Image format (jfif) is not supported.'));
            return redirect()->back();
        }

        if ($subject = $this->repository->create($data)) {
            $subjectRepo = new SubjectRepository($subject);
            if (is_array($request->content_authors) && count($request->content_authors)) {
                $subjectRepo->attachContentAuthors($request->content_authors);
            }
            if (is_array($request->instructors) && count($request->instructors)) {
                $subjectRepo->attachInstructors($request->instructors);
            }
            $this->sendEmailToContentAuthorsAndInstructorCreated($subject);
            
            if(!is_null($subject->country_id) && $subject->country_id == env('fall_back_country_id'))
            {
                $this->publishSubjectToOurEdu($subject);
            }

            flash()->success(trans('app.Created successfully'));

            SubjectModified::dispatch($request->except('image', '_token', '_method'), $subject->toArray(), 'Subject created');

            return redirect()->route('admin.subjects.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function sendEmailToContentAuthorsAndInstructorCreated($subject)
    {
        $subjectRepository = new SubjectRepository($subject);


        $contentAuthors = $subjectRepository->getContentAuthors()->pluck('email')->toArray();
        $instructors = $subjectRepository->getInstructors()->pluck('email')->toArray();
        $sme = $subjectRepository->getSme();

        $this->sendEmailToContentAuthorsAndInstructor($subject, $sme, $contentAuthors, $instructors);
    }

    public function sendEmailToContentAuthorsAndInstructor($subject, $sme, $contentAuthors, $instructors)
    {
        // Notifying SME
        if (isset($sme)) {
            $smeNotificationData = [
                'users' => collect([$sme]),
                'mail' => [
                    'user_type' => UserEnums::SME_TYPE,
                    'data'=>  ['subject' => $subject, 'url' => ''
                    , 'lang' => App::getLocale()
                ],
                    'subject' => trans('subject.Admin assigned you to subject', [], App::getLocale()),
                    'view' => 'SubjectAssigned'
                ],
                'fcm' => [
                    'data' => [
                        'title' => 'notification.admin_assign_you_to_subject',
                        'body' => 'notification.admin_assign_you_to_subject',
//                        'screen_type' => NotificationEnum::ADMIN_ASSIGN_SME_TO_SUBJECT,
                        'url' => getDynamicLink(DynamicLinksEnum::ADMIN_ASSIGN_SME_SUBJECT, ['subject_id' => $subject->id,'portal_url' => env('SME_PORTAL_URL')]),
                    ]
                ]
            ];
            $this->notifierFactory->send($smeNotificationData);
        }

        // Notifying Content Authors
        if (isset($contentAuthors) && count($contentAuthors)) {
            $contentAuthors = $this->userRepository->getUsersByEmail($contentAuthors);
            $contentAuthorsNotificationData = [
                'users' => $contentAuthors,
                'mail' => [
                    'user_type' => UserEnums::CONTENT_AUTHOR_TYPE,
                    'data'=>  ['subject' => $subject, 'url' => '',
                    'lang' => App::getLocale()],
                    'subject' => trans('subject.Admin assigned you to subject', [], App::getLocale()),
                    'view' => 'SubjectAssigned'
                ],
                'fcm' => [
                    'data' => [
                        'title' => 'notification.admin_assign_you_to_subject',
                        'body' => 'notification.admin_assign_you_to_subject',
//                        'screen_type' => NotificationEnum::ADMIN_ASSIGN_SME_TO_SUBJECT,
//                        'url' => getDynamicLink(DynamicLinksEnum::ADMIN_ASSIGN_SME_SUBJECT, ['subject_id' => $subject->id,'portal_url' => env('SME_PORTAL_URL')]),
                    ]
                ]
            ];
            $this->notifierFactory->send($contentAuthorsNotificationData);
        }

        // Notifying instructors
        if (isset($instructors) && count($instructors)) {
            $instructors = $this->userRepository->getUsersByEmail($instructors);
            $instructorsNotificationData = [
                'users' => $instructors,
                'mail' => [
                    'user_type' => UserEnums::INSTRUCTOR_TYPE,
                    'data'=>  ['subject' => $subject, 'url' => '',
                    'lang' => App::getLocale()],
                    'subject' => trans('subject.Admin assigned you to subject', [], App::getLocale()),
                    'view' => 'SubjectAssigned'
                ],
                'fcm' => [
                    'data' => [
                        'title' => 'notification.admin_assign_you_to_subject',
                        'body' => 'notification.admin_assign_you_to_subject',
//                        'screen_type' => NotificationEnum::ADMIN_ASSIGN_SME_TO_SUBJECT,
//                        'url' => getDynamicLink(DynamicLinksEnum::ADMIN_ASSIGN_SME_SUBJECT, ['subject_id' => $subject->id,'portal_url' => env('SME_PORTAL_URL')]),
                    ]
                ]
            ];
            $this->notifierFactory->send($instructorsNotificationData);
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $subjectRepo = new SubjectRepository($data['row']);
        $data['selectedContentAuthors'] = $subjectRepo->getContentAuthorsIds();
        $data['selectedInstructors'] = $subjectRepo->getInstructorsIds();

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjects.get.index')];

        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function putEdit(SubjectRequest $request, $id)
    {
        $contentAuthors = $request->content_authors ?? [];
        $instructors = $request->instructors ?? [];
        $row = $this->repository->findOrFail($id);
        $old = $this->repository->findOrFail($id)->toArray();

        $subjectRepo = new SubjectRepository($row);

        $oldContentAuthors = $subjectRepo->getContentAuthors();
        $oldInstructors = $subjectRepo->getInstructors();
        $oldSme = $subjectRepo->getSme();
        if ($subject = $subjectRepo->update($request->all())) {
            if (is_array($contentAuthors)) {
                $subjectRepo->syncContentAuthors($contentAuthors);
            }
            if (is_array($request->instructors) && count($request->instructors) > 0) {
                $subjectRepo->syncInstructors($request->instructors);
            } else {
                $subjectRepo->syncInstructors([]);
            }

            $newContentAuthors = $subjectRepo->getContentAuthors();
            $newInstructors = $subjectRepo->getInstructors();

            $subjectRepo = new SubjectRepository($subject);
            $newSme = $subjectRepo->getSme();

            $this->sendEmailToContentAuthorsAndInstructorUpdated(
                $subject,
                $oldSme,
                $newSme,
                $oldContentAuthors,
                $oldInstructors,
                $newContentAuthors,
                $newInstructors
            );

            if(!is_null($subject->country_id) && $subject->country_id == env('fall_back_country_id'))
            {
                $this->publishSubjectToOurEdu($subject);
            }

            flash()->success(trans('app.Update successfully'));

            SubjectModified::dispatch($request->except('image', '_token', '_method'), $old, 'Subject updated');

            return redirect()->route('admin.subjects.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function sendEmailToContentAuthorsAndInstructorUpdated(
        $subject,
        $oldSme,
        $newSme,
        $oldContentAuthors,
        $oldInstructors,
        $newContentAuthors,
        $newInstructors
    )
    {
        $sme = null;
        if (isset($oldSme) && isset($newSme)) {
            if (!$oldSme->is($newSme)) {
                $sme = $newSme;
            }
        } elseif (isset($newSme) && !isset($oldSme)) {
            $sme = $newSme;
        }


        $contentAuthors = $newContentAuthors->diff($oldContentAuthors)->pluck('email')->toArray();
        $instructors = $newInstructors->diff($oldInstructors)->pluck('email')->toArray();


        $this->sendEmailToContentAuthorsAndInstructor($subject, $sme, $contentAuthors, $instructors);
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjects.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);

        if ($row->is_aptitude) {
            flash()->error(trans('app.cannot delete aptitude subject'));
            return redirect()->route('admin.subjects.get.index');
        }

        $rep = new SubjectRepository($row);
        if ($rep->delete()) {
            flash()->success(trans('app.Deleted Successfully'));
            SubjectModified::dispatch([], $row->toArray(), 'Subject deleted');
            return redirect()->route('admin.subjects.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));


        return redirect()->back();
    }

    public function getEducationalSystem()
    {
        if ($countryId = request('country_id')) {
            $educationalSystem = $this->educationalSystemRepository->pluckByCountryId($countryId);

            return response()->json(
                [
                    'status' => '200',
                    'educationSystem' => $educationalSystem
                ]
            );
        }
    }

    public function getSubjectTasks($id)
    {
        $this->setTaskFilters();
        $data['filters'] = $this->filters;
        $data['page_title'] = trans('subject.tasks');
        $data['breadcrumb'] = [trans('navigation.Subjects') => route('admin.subjects.get.index')];
        $subject = $this->repository->findOrFail($id);
        $subjectRepo = new SubjectRepository($subject);


        $data['tasks'] = $subjectRepo->getSubjectTasks($this->filters);
        return view($this->parent . '.' . 'tasks' . '.view', $data);
    }

    public function setTaskFilters()
    {
        $options = Option::whereIn('type', [
            OptionsTypes::RESOURCE_DIFFICULTY_LEVEL,
            OptionsTypes::RESOURCE_LEARNING_OUTCOME,
        ])->get();

        $this->filters[] = [
            'name' => 'difficulty_level',
            'type' => 'relation',
            'key' => 'accept_criteria->difficulty_level',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => $options->where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('difficulty_level'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.difficulty_level'),
                'placeholder' => trans('subject.difficulty_level'),
            ]
        ];

        $this->filters[] = [
            'name' => 'learning_outcome',
            'type' => 'relation',
            'key' => 'accept_criteria->learning_outcome',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => $options->where('type', OptionsTypes::RESOURCE_LEARNING_OUTCOME)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('learning_outcome'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.learning_outcome'),
                'placeholder' => trans('subject.learning_outcome'),
            ]
        ];
        $this->filters[] = [
            'name' => 'resource_type',
            'type' => 'relation',
            'key' => 'resource_slug',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => Resource::get()->pluck('title', 'slug')->toArray(),
            'trans' => false,
            'value' => request()->get('resource_type'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.resource_type'),
                'placeholder' => trans('subject.resource_type'),
            ]
        ];
    }

    public function pauseAndUnpause($id)
    {
        $subject = $this->repository->findOrFail($id);
        if ($subject->is_aptitude) {
            flash()->error(trans('app.failed to do this action'));
            return redirect()->route('admin.subjects.get.index');
        }
        if ($subject) {
            $subject = $this->repository->findOrFail($id);
            if ($subject->is_active == 0) {
                $subject->is_active = 1;
                $subject->save();
                flash(trans('app.Pause Removed Successfully'))->success();
                SubjectModified::dispatch([], $subject->toArray(), 'Subject resumed');
                SubjectResumedEvent::dispatch($subject);
            } else {
                $subject->is_active = 0;
                $subject->save();
                flash(trans('app.Subject Paused Successfully'))->success();
                SubjectModified::dispatch([], $subject->toArray(), 'Subject paused');
                SubjectPausedEvent::dispatch($subject);
            }
            return redirect()->route('admin.subjects.get.index');
        }
        flash()->error(trans('app.failed to do this action'));
        return redirect()->route('admin.subjects.get.index');
    }


    /*
     *
     * Get Subjects with count exams and rate of success result
     *
     * */

    public function getSubjectWithCountExamsAndRateResult()
    {
        $this->setStudentGradesFilters();
        $data['filters'] = $this->filters;
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Student Grades');
        $data['breadcrumb'] = '';

        $data['rows'] = $this->repository->getSubjectWithSuccessRateAndExamCount($this->filters);
        return view($this->parent . '.' . $this->module . '.studentGrades', $data);
    }

    public function setStudentGradesFilters()
    {
        $this->filters[] = [
            'name' => 'country_id',
            'type' => 'select',
            'data' => $this->countryRepository->pluck('id', 'name'), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('country_id'),
            'attributes' => [
                'id' => 'country_id',
                'class' => 'form-control',
                'label' => trans('exams.Country'),
                'placeholder' => trans('exams.Country')
            ]
        ];

        $this->filters[] = [
            'name' => 'educational_system_id',
            'type' => 'select',
            'data' => [],
            'value' => request()->get('educational_system_id'),
            'attributes' => [
                'id' => 'educational_system_id',
                'class' => 'form-control',
                'label' => trans('exams.Educational System'),
                'placeholder' => trans('exams.Educational System')
            ]
        ];
    }

    public function exportRateResult()
    {
        $this->setStudentGradesFilters();

        $data['filters'] = $this->filters;

        $data['rows'] = $this->repository->getExportSubjectWithSuccessRateAndExamCount($this->filters);

        return Excel::download(new SuccessRateExport($data['rows'], $this->exportRateResultHeading()), $this->module . '-success-rates.xlsx');
    }

    private function exportRateResultHeading()
    {
        return [
            trans('exams.Subject'),
            trans('exams.Number of exams'),
            trans('exams.Result'),
            trans('exams.Educational system'),
            trans('exams.Country'),
            trans('exams.Grade class'),
        ];
    }

    /**
     * Export subjects to excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportSubjects()
    {
        $data['rows'] = $this->repository->dataExport();

        return Excel::download(new SubjectsExport($data['rows']), $this->module . '-data.xlsx');
    }
    public function exportSubjectsNamesAndImages()
    {
        $data['rows'] = $this->repository->dataExport();

        return Excel::download(new SubjectsNamesAndImagesExport($data['rows']), $this->module . '-names-images.xlsx');
    }

    private function publishSubjectToOurEdu(Subject $subject)
    {
        $ourEduReference = $subject->our_edu_reference;

        $payload = [
            'our_edu_reference' => $ourEduReference,
            'name_en' => $subject->name,
            'name_ar' => $subject->name,
            'ta3lom_reference' => $subject->id,
            'educational_system' => [
                'our_edu_reference' => $subject->educationalSystem->our_edu_reference,
                'name_en' => $subject->educationalSystem->translate('en')->name,
                'name_ar' => $subject->educationalSystem->translate('ar')->name,
                'ta3lom_reference' => $subject->educationalSystem->id
            ],
            'semester' => [
                'our_edu_reference' => $subject->educationalTerm->our_edu_reference,
                'name_en' => $subject->educationalTerm->translate('en')->title,
                'name_ar' => $subject->educationalTerm->translate('ar')->title,
                'ta3lom_reference' => $subject->educationalTerm->id
            ],
            'academic_year' => [
                'our_edu_reference' => $subject->academicalYears->our_edu_reference,
                'name_en' => $subject->academicalYears->translate('en')->title,
                'name_ar' => $subject->academicalYears->translate('ar')->title,
                'ta3lom_reference' => $subject->academicalYears->id
            ],
            'grade' => [
                'our_edu_reference' => $subject->gradeClass->our_edu_reference,
                'name_en' => $subject->gradeClass->translate('en')->title,
                'name_ar' => $subject->gradeClass->translate('ar')->title,
                'ta3lom_reference' => $subject->gradeClass->id
            ]
        ];
        if (!is_null($ourEduReference)) {
            SubjectUpdated::publish($payload);
        } else {
            SubjectCreated::publish($payload);
        }
    }

}
