<?php

namespace App\OurEdu\Courses\Admin\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Illuminate\Database\Eloquent\Collection;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Courses\Repository\CourseRepository;
use App\OurEdu\Courses\Admin\Requests\CourseRequest;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\BaseNotification\Jobs\FinishVCRSessionJob;
use App\OurEdu\Courses\Admin\Requests\UpdateCourseRequest;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\BaseNotification\Jobs\NotificationStudentsJob;
use App\OurEdu\Courses\Middleware\CheckCourseUsageMiddleware;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Courses\Admin\Requests\CreateCourseSessionRequest;
use App\OurEdu\BaseNotification\Jobs\InstructorSessionNotification;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;

class CourseController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;
    private $userRepository;
    private $subjectRepository;
    private $VCRSessionRepository;
    private VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository;
    private $params;

    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository,
        VCRSessionRepositoryInterface $VCRSessionRepository,
        VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository,
        
    ) {
        $this->module = 'courses';
        $this->subjectRepository = $subjectRepository;
        $this->repository = $courseRepository;
        $this->userRepository = $userRepository;
        $this->VCRSessionRepository = $VCRSessionRepository;

        $this->title = trans('courses.Courses');
        $this->params['title'] = trans('course_sessions.Course Sessions');
        $this->params['module'] = 'courseSessions';
        $this->parent = ParentEnum::ADMIN;
        $this->middleware(CheckCourseUsageMiddleware::class)->only('delete');
        $this->VCRSessionParticipantsRepository = $VCRSessionParticipantsRepository;
    }

    public function getIndex()
    {
        $data['rows'] = $this->repository->paginate(14);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.courses.get.index')];

        $data['row'] = new Course;

        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.create', $data);
    }


    public function postCreate(CourseRequest $request)
    {
        $data = $request->except(['sessions']);

        if ($course = $this->repository->create($data)) {
            $sessions = $request->sessions;
            $courseSessions = $course->sessions()->createMany($sessions);
            $this->createVCRSessions($courseSessions, $course);

            // add sessions created after course, update audits log
            $this->repository->addSessionsToLog($course);
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.courses.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.courses.get.index')];

        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(UpdateCourseRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);
        $update = $this->repository->setCourse($row)->update($request->all());
        if ($update) {
        // if instructor_id updated
            if($row->wasChanged('instructor_id')){
                // update course students by the new instructor_id
                $row->students()->update(['instructor_id' => $request->instructor_id ]);
                //update vcr sessions related to this course
                $this->updateVCRSessions($row->sessions, $row);
            }
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.courses.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.courses.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);

        $rep = new CourseRepository($row);
        if ($rep->delete()) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.courses.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function lookup()
    {
        $data['types'] = CourseEnums::getFormattedTypes();
        if (array_key_exists("live_session", $data["types"])) {
            unset($data["types"]['live_session']);
        }

        $data['subjects'] = $this->subjectRepository->pluck();
        $data['instructors'] = $this->userRepository->getPluckUserByType(UserEnums::INSTRUCTOR_TYPE);
        return $data;
    }

    public function getCourseSessions($id)
    {
        $course = $this->repository->findOrFail($id);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        $data['course'] = $course;
        $data['rows'] = $this->repository->getCourseSessions($id);
        return view($this->parent . '.' . $this->params['module'] . '.index', $data);
    }

    private function createVCRSessions($sessions, $course)
    {
        $subjectName = $course->subject?->name;
        foreach ($sessions as $session) {
            $VCRsession = $this->VCRSessionRepository->create([
                'instructor_id' => $course->instructor_id,
                'subject_id' => $course->subject_id,
                'subject_name' => $subjectName,
                'course_id' => $course->id,
                'vcr_session_type' => VCRSessionEnum::COURSE_SESSION_SESSION,
                'course_session_id' => $session->id,
                'room_uuid' => substr(Str::uuid(),0,30),
                'agora_instructor_uuid' => Str::uuid(),
                'time_to_start'=>date('Y-m-d H:i:s', strtotime("{$session->date} {$session->start_time}")),
                'time_to_end'=>date('Y-m-d H:i:s', strtotime("{$session->date} {$session->end_time}")),
            ]);

            $this->notifyInstructor($VCRsession);
        }
    }

    private function notifyInstructor(VCRSession $VCRSession)
    {
        if ((new Carbon($VCRSession->time_to_start))->gt(now()->addMinutes(15))) {
            return ;
        }
        NotificationStudentsJob::dispatch(new Collection([]), $VCRSession, true)
            ->delay((new Carbon($VCRSession->time_to_start)))
            ->onQueue('sessions');
        FinishVCRSessionJob::dispatch($VCRSession)
            ->delay((new Carbon($VCRSession->time_to_end)))->onQueue('sessions');
        $VCRSession->update(['is_notified'=>1]);
    }

    private function updateVCRSessions($sessions, $course)
    {
        foreach ($sessions as $session) {
            $vcrSession = $this->VCRSessionRepository->findVCRSessionByCourseSession($course->id, $session->id);
            $this->VCRSessionRepository->update($vcrSession->id ,[
                'instructor_id' => $course->instructor_id,
            ]);
            $this->notifyInstructor($vcrSession);
        }

    }

    public function getSession($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.Create') . ' ' . $this->params['title'];
        $data['breadcrumb'] = [$this->params['title'] => route('admin.courses.get.create.session', $data['row']->id)];
        return view($this->parent . '.' . $this->params['module'] . '.create', $data);
    }

    public function postSession(CreateCourseSessionRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $sessions = $request->sessions;
        $courseSessions = $course->sessions()->createMany($sessions);
        $this->createVCRSessions($courseSessions, $course);
        $students = $this->repository->getStudentsSubscribedCourse($course);
        if (count($students)) {
            foreach ($courseSessions as $session) {
                foreach ($students as $student) {
                    $participationData [] = [
                        'participant_uuid' => Str::uuid(),
                        'vcr_session_id' => $session->VCRSession->id,
                        'user_id' => $student->user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            $this->VCRSessionParticipantsRepository->insert($participationData);
        }
        $this->repository->addSessionsToLog($course);
        flash()->success(trans('app.Created successfully'));
        return redirect()->route('admin.courses.get.course.sessions', $course->id);
    }
}
