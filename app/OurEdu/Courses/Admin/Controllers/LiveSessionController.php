<?php

namespace App\OurEdu\Courses\Admin\Controllers;

use App\Events\LiveSessionCanceled;
use App\OurEdu\Courses\Admin\Events\CourseSessionCanceled;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseNotification\Jobs\InstructorSessionNotification;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Courses\Repository\LiveSessionRepository;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Courses\Admin\Requests\LiveSessionRequest;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Courses\Repository\LiveSessionRepositoryInterface;
use App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionDeleted;
use App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionUpdated;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Live Session controller
 */
class LiveSessionController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;
    private $userRepository;
    private $subjectRepository;
    private $VCRSessionRepository;

    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        LiveSessionRepositoryInterface $liveSessionRepository,
        UserRepositoryInterface $userRepository,
        VCRSessionRepositoryInterface $VCRSessionRepository
    ) {
        $this->module = 'liveSessions';
        $this->subjectRepository = $subjectRepository;
        $this->repository = $liveSessionRepository;
        $this->userRepository = $userRepository;
        $this->VCRSessionRepository = $VCRSessionRepository;

        $this->title = trans('live_sessions.Live sessions');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $data['rows'] = $this->repository->paginate();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.liveSessions.get.index')];

        $data['row'] = new LiveSession;

        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.create', $data);
    }


    public function postCreate(LiveSessionRequest $request)
    {
        if ($liveSession = $this->repository->create($request->all())) {
            if (!Str::contains($liveSession->picture,'live-lessons/')) {
                $liveSession->update(['picture' => 'live-lessons/'.$liveSession->picture]);
            }
            $session = $request->only('date', 'content', 'start_time', 'end_time');
            $courseSession = $liveSession->sessions()->create($session);
            $this->createVCRSessions($courseSession, $liveSession);

            $this->repository->addSessionTimeToLog($liveSession);

            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.liveSessions.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.liveSessions.get.index')];

        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(LiveSessionRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);

        if (Carbon::parse($row->date . ' ' . $row->start_time)
            ->lessThanOrEqualTo(Carbon::now())) {
            $request['date'] = $row->session->date;
            $request['start_time'] = $row->session->start_time;
            $request['end_time'] = $row->session->end_time;
        }

            $oldLiveSession = $this->repository->findOrFail($id);

        if ($this->repository->setLiveSession($row)
            ->update($request->all())) {
            if (!is_null($row->image) && !Str::contains($row->picture,'live-lessons/')) {
                $row->update(['picture' => 'live-lessons/'.$row->picture]);
            }
            // if instructor_id updated
            if ($oldLiveSession->instructor_id != $row->instructor_id) {
                // update live session students by the new instructor_id
                $row->students()->update(['instructor_id' => $request->instructor_id ]);
                //update vcr sessions related to this course
                $this->updateVCRSessions($row->session, $row);
            }
            event(new LiveSessionUpdated($row->load('session')->toArray()));
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.liveSessions.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['row']->load('session');
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.liveSessions.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);

        $rep = new LiveSessionRepository($row);
        if ($rep->delete()) {
            event(new LiveSessionDeleted($row->load('session')->toArray()));
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.liveSessions.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function lookup()
    {
        $data['subjects'] = $this->subjectRepository->pluck();
        $data['instructors'] = $this->userRepository->getPluckUserByType(UserEnums::INSTRUCTOR_TYPE);

        return $data;
    }

    private function createVCRSessions($courseSession, $liveSession)
    {
        $VCRsession = VCRSession::create([
            'instructor_id' => $liveSession->instructor_id,
            'subject_id' => $liveSession->subject_id,
            'subject_name' => $liveSession->subject->name,
            'course_id' => $liveSession->id,
            'vcr_session_type' => VCRSessionEnum::LIVE_SESSION_SESSION,
            'course_session_id' => $courseSession->id,
            'room_uuid' => substr(Str::uuid(),0,30),
            'agora_instructor_uuid' => Str::uuid(),
            'time_to_start'=>date('Y-m-d H:i:s', strtotime("{$liveSession->date} {$courseSession->start_time}")),
            'time_to_end'=>date('Y-m-d H:i:s', strtotime("{$liveSession->date} {$courseSession->end_time}")),
        ]);

        InstructorSessionNotification::dispatch($VCRsession)
                ->delay((new Carbon($VCRsession->time_to_start)));
    }

    private function updateVCRSessions($session, $course)
    {
        $vcrSession = $this->VCRSessionRepository->findVCRSessionByCourseSession($course->id, $session->id);
        $this->VCRSessionRepository->update($vcrSession->id,[
            'instructor_id' => $course->instructor_id,
        ]);
    }

    public function cancel($id)
    {
        $row = $this->repository->findOrFail($id);
        $session = $row->session ?? null;

        if ($session) {
            if ($session->status == CourseSessionEnums::ACTIVE) {
                    $session->update(['status' => CourseSessionEnums::CANCELED]);
                flash()->success(trans('app.Canceled successfully'));

                event(new \App\OurEdu\Courses\Admin\Events\LiveSessions\LiveSessionCanceled($row));

                return redirect()->route('admin.liveSessions.get.index');
            }
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
}
