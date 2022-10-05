<?php

namespace App\Modules\Users\Admin\Controllers;

use App\Modules\Assessments\Jobs\AddUserToAssessmentJob;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\Modules\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\Modules\Invitations\Enums\InvitationEnums;
use App\Modules\Options\Enums\OptionsTypes;
use App\Modules\Options\Repository\OptionRepositoryInterface;
use App\Modules\SchoolAccounts\SchoolAccounts\Repository\SchoolAccountRepository;
use App\Modules\Users\Admin\Requests\AttachStudentTeacherRequest;
use App\Modules\Users\Models\StudentTeacherStudent;
use App\Modules\Users\Repository\StudentRepositoryInterface;
use App\Modules\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\Modules\Users\User;
use FontLib\Table\Type\name;
use App\Modules\Users\UserEnums;
use App\Modules\Helpers\MailManger;
use App\Http\Controllers\Controller;
use App\Modules\BaseApp\Helpers\Mail;
use App\Modules\BaseApp\Enums\ParentEnum;
use App\Modules\Users\Events\UserCreated;
use App\Modules\BaseApp\Helpers\MailClass;
use App\Modules\Users\Events\UserModified;
use App\Modules\Users\Admin\Jobs\SendRegisterEMail;
use App\Modules\Users\Admin\Requests\CreateUserRequest;
use App\Modules\Users\Admin\Requests\UpdateUserRequest;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\Admin\Requests\InsturctorStudentsRequest;
use App\Modules\Users\Repository\InstructorRepositoryInterface;
use App\Modules\Users\Repository\ContentAuthorRepositoryInterface;
use App\Modules\Users\UseCases\CreateUserUseCase\CreateUserUseCaseInterface;
use App\Modules\Users\UseCases\UpdateUserUseCase\UpdateUserUseCaseInterface;
use App\Modules\Users\UseCases\SuspendUserUseCase\SuspendUserUseCaseInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Audit;

class UsersController extends Controller
{
    private $title;
    private $module;
    private $repository;
    private $contentAuthor;
    private $parent;
    private $schoolRepository;
    private $countryRepository;
    private $instructorRepository;
    private $educationalSystemRepository;
    private $optionRepository;
    private $gradeClassRepository;
    private $studentRepository;
    private $filters = [];
    /**
     * @var CreateZoomUserUseCaseInterface
     */
    private $createZoomUser;
    private $schoolAccountRepository;


    public function __construct(
        UserRepositoryInterface $userRepository,
        ContentAuthorRepositoryInterface $contentAuthorRepository,
        InstructorRepositoryInterface $instructorRepository,
        CountryRepositoryInterface $countryRepository,
        EducationalSystemRepositoryInterface $educationalSystemRepository,
        OptionRepositoryInterface $optionRepository,
        GradeClassRepositoryInterface $gradeClassRepository,
        StudentRepositoryInterface $studentRepository,
        CreateZoomUserUseCaseInterface  $createZoomUser,
        SchoolAccountRepository $schoolAccountRepository
    ) {
        $this->module = 'users';
        $this->title = trans('app.Users');
        $this->repository = $userRepository;
        $this->parent = ParentEnum::ADMIN;
        $this->contentAuthor = $contentAuthorRepository;
        $this->instructorRepository = $instructorRepository;
        $this->countryRepository = $countryRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;
        $this->optionRepository = $optionRepository;
        $this->gradeClassRepository = $gradeClassRepository;
        $this->studentRepository = $studentRepository;
        $this->createZoomUser = $createZoomUser;
        $this->schoolAccountRepository = $schoolAccountRepository;
    }

    public function getIndex()
    {

        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Users');
        $data['breadcrumb'] = '';
        $data['rows'] = $this->repository->all($this->filters);
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {

        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Create') . " " . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.users.get.index')];
        $data = array_merge($data, $this->lookup());
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(CreateUserRequest $request, CreateUserUseCaseInterface $createUserUseCase)
    {
        $request->merge(['confirmed'=>1]);
        DB::beginTransaction();
        if ($row = $createUserUseCase->createUser($this->repository, $request->all())) {
            $createZoomUser = $this->createZoomUser->createUser($row);
            if ($createZoomUser['error']) {
                DB::rollBack();
                Log::channel('slack')->error($createZoomUser['detail']);
                flash()->error($createZoomUser['detail']);
                return redirect()->route('admin.users.get.index');
            }
            if(in_array($row->type, UserEnums::assessmentUsers())){
                AddUserToAssessmentJob::dispatch($row);
            }
            if(!is_null($row->email))
            {
                $newMail = new MailManger();
                $data = [
                    'user_type' => $row->type,
                    'data' => ['user'=>$row, 'lang' => $row->language],
                    'subject' => trans('app.Activate Account', [], $row->language),
                    'emails' => [$row->email],
                    'view' => 'RegisterNotification'
                ];

                $newMail->prepareMail($data);
                $newMail->handle();
            }


            UserModified::dispatch($request->except('profile_picture', '_token', '_method'), $row->toArray(), 'User created');
            DB::commit();

            flash()->success(trans('app.Created successfully'));

            return redirect()->route('admin.users.get.index');
        }
        flash()->error(trans('app.failed to save'));
        return redirect()->route('admin.users.get.index');
    }


    public function getEdit($id)
    {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Edit') . " " . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.users.get.index')];
        $data['row'] = $this->repository->findOrFail($id);
        $data['userEnums'] = UserEnums::class;
        $data['relation'] = $this->getUserRelation($data['row']);
        $data = array_merge($data, $this->lookup());
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    private function getUserRelation(User $user)
    {
        $relation = null;
        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            $relation = $this->contentAuthor->getContentAuthorByUserId($user->id);
        }
        if ($user->type == UserEnums::INSTRUCTOR_TYPE) {
            $relation = $this->instructorRepository->getInstructorByUserId($user->id);
        }
        if ($user->type == UserEnums::STUDENT_TYPE) {
            $relation = $this->studentRepository->getStudentByUserId($user->id);
        }
        return $relation;
    }

    public function postEdit(UpdateUserRequest $request, UpdateUserUseCaseInterface $updateUserUseCase, $id)
    {
        $user = $this->repository->findOrFail($id);
        $oldData = $user->toArray();
        if ($updateUserUseCase->updateUser($this->repository, $request->all(), $id)) {
            UserModified::dispatch($request->except('_token', '_method', 'password', 'password_confirmation', 'profile_picture'), $oldData, 'User updated');
            if (isset($user->student->id)) {
                $this->repository->addIpAndUserAgentToLogs($id, isset($request->all()['country_id']));
            }
            flash(trans('app.Update successfully'))->success();
            return redirect()->route('admin.users.get.index');
        }
        flash()->error(trans('app.failed to save'));
        return redirect()->route('admin.users.get.index');
    }

    public function getView($id)
    {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.View') . " " . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.users.get.index')];
        $data['row'] = $this->repository->findOrFail($id);
        $data['relation'] = $this->getUserRelation($data['row']);
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function getDelete($id)
    {
        $row = $this->repository->findOrFail($id);
        if ($row->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            $content = $this->contentAuthor->getContentAuthorByUserId($row->id);
            $this->contentAuthor->delete($content);
        }
        if ($row->type == UserEnums::INSTRUCTOR_TYPE) {
            $instructor = $this->instructorRepository->getInstructorByUserId($row->id);
            $this->instructorRepository->delete($instructor);
        }
        if ($row->type == UserEnums::SCHOOL_INSTRUCTOR){
            if($row->schoolInstructorSessions()->where('from','>=',now())->exists()){
                flash()->error(trans('app.You should delete his sessions first'));
                return redirect()->back();
            }
        }
        $this->repository->delete($row);

        UserModified::dispatch([], $row->toArray(), 'User deleted');

        flash()->success(trans('app.Deleted Successfully'));
        return redirect()->route('admin.users.get.index');
    }

    //Should Work like toggle of suspend user if not suspended and remove suspend if suspended
    public function getSuspend($id, SuspendUserUseCaseInterface $suspendUserUseCase)
    {
        if ($suspendUserUseCase->suspendUser($this->repository, $id)) {
            $user = $this->repository->findOrFail($id);
            if ($user->suspended_at) {
                flash(trans('app.Suspended Successfully'))->success();

                UserModified::dispatch([], $user->toArray(), 'User suspended');
            } else {
                flash(trans('app.Suspension Removed Successfully'))->success();
                UserModified::dispatch([], $user->toArray(), 'User suspension removed');
            }
            return redirect()->route('admin.users.get.index');
        }
        flash()->error(trans('app.failed to do this action'));
        return redirect()->route('admin.users.get.index');
    }

    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'first_name',
            'type' => 'input',
            'trans' => false,
            'value' => request()->get('first_name'),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('users.first_name'),
                'placeholder'=>trans('users.first_name'),
            ]
        ];
        $this->filters[] = [
            'name' => 'last_name',
            'type' => 'input',
            'trans' => false,
            'value' => request()->get('last_name'),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('users.last_name'),
                'placeholder'=>trans('users.last_name'),
            ]
        ];
        $this->filters[] = [
            'name' => 'email',
            'type' => 'input',
            'trans' => false,
            'value' => request()->get('email'),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('users.email'),
                'placeholder'=>trans('users.email'),
            ]
        ];
        $this->filters[] = [
            'name' => 'type',
            'type' => 'select',
            'data' => UserEnums::filterableUserType(), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('type'),
            'attributes' => [
                'id'=>'country_id',
                'class'=>'form-control',
                'label'=>trans('users.type'),
                'placeholder'=>trans('users.type')
            ]
        ];
        $this->filters[] = [
            'name' => 'username',
            'type' => 'input',
            'value' => request()->get('username'),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('users.username'),
                'placeholder'=>trans('users.username'),
            ]
        ];
    }

    public function editStudents($id)
    {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Instructed students for') . " " . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.users.get.index')];
        $data['row'] = $this->repository->findOrFail($id);
        $data['students'] = $this->repository->pluckStudentsMail();

        return view($this->parent . '.' . $this->module . '.students', $data);
    }

    public function updateStudents(InsturctorStudentsRequest $request, $id)
    {
        $user = $this->repository->findOrFail($id);

        $user->instructedStudents()->sync($request->instructedStudents);

        UserModified::dispatch($request->except('_token', '_method'), $user->toArray(), 'Instructor students update');


        flash(trans('app.Update successfully'))->success();
        return back();
    }

    public function indexStudentStudentTeachers($studentId)
    {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.View Student Student Teacher');
        $data['breadcrumb'] = [$this->title => route('admin.users.get.index')];
        $data['student'] = $this->repository->findOrFail($studentId);
        return view($this->parent . '.' . $this->module .'.student_student_teacher'.'.index', $data);
    }

    public function getAddStudentTeacherToStudent($studentId)
    {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.View Student Student Teacher');
        $data['breadcrumb'] = [$this->title => route('admin.users.get.index')];
        $data['student'] = $this->repository->findOrFail($studentId);
        $data['subjects'] = $data['student']->student->subjects->pluck('name', 'id');
        $data['student_teachers'] = $this->repository->getPluckUserByType(UserEnums::STUDENT_TEACHER_TYPE);
        return view($this->parent . '.' . $this->module .'.student_student_teacher'.'.add', $data);
    }

    public function postAddStudentTeacherToStudent(AttachStudentTeacherRequest $request, $studentId)
    {
        $studentTeacherStudent = new StudentTeacherStudent();
        $relation = $studentTeacherStudent->where('student_teacher_id', $request->student_teacher_id)
            ->where('student_id', $studentId)
            ->first();
        if (!is_null($relation)) {
            $assignedToThisSubject = $relation->subjects()->where('subject_id', $request->subject_id)->exists();
            if ($assignedToThisSubject) {
                flash()->error(trans('app.this student teacher already assigned to this subject'));
                return redirect()->back();
            }
            $relation->subjects()->attach($request->subject_id);
            flash(trans('app.Added successfully'))->success();
            return redirect()->route('admin.users.index.student.student-teacher', ['studentId' => $studentId]);
        }else {
            $pivot = StudentTeacherStudent::create([
                'student_teacher_id'    =>  $request->student_teacher_id,
                'student_id'    =>  $studentId,
                'status'    =>  InvitationEnums::ACCEPTED,
            ]);
            $pivot->subjects()->attach($request->subject_id);
            flash(trans('app.Added successfully'))->success();
            return redirect()->route('admin.users.index.student.student-teacher', ['studentId' => $studentId]);
        }
    }

    public function detachStudentTeacherFromStudent($studentId, $studentTeacherId)
    {
        $studentTeacherStudent = new StudentTeacherStudent();

        $studentTeacherStudent = $studentTeacherStudent->where('student_teacher_id', $studentTeacherId)
            ->where('student_id', $studentId)
            ->firstOrFail();
        $detachSubjects = $studentTeacherStudent->subjects()->detach();
        if ($detachSubjects) {
            $studentTeacherStudent->delete();
            flash(trans('app.Deleted Successfully'))->success();
            return redirect()->route('admin.users.index.student.student-teacher', ['studentId' => $studentId]);
        }
        flash(trans('app.Something went wrong'))->error();
        return redirect()->route('admin.users.index.student.student-teacher', ['studentId' => $studentId]);
    }

    public function lookup()
    {
        $data['educationalSystems'] = $this->educationalSystemRepository->pluck()->toArray();
        $data['gradeClasses'] = $this->gradeClassRepository->pluck();
        $data['academicalYears'] = $this->optionRepository->pluckByType(OptionsTypes::ACADEMIC_YEAR);
        $data['userType'] = UserEnums::availableUserType();
        $data['countries'] = $this->countryRepository->pluck();
        $data['schools'] = $this->schoolRepository->pluck();
        $data['school_accounts'] = $this->schoolAccountRepository->pluck();
        return $data;
    }

}
