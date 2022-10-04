<?php

namespace App\OurEdu\Courses\Admin\Controllers;

use App\OurEdu\BaseNotification\Jobs\NotificationStudentsJob;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Courses\Admin\Events\CourseSessionUpdated;
use App\OurEdu\Courses\Admin\Events\CourseSessionCanceled;
use App\OurEdu\Courses\Admin\Requests\CourseSessionRequest;
use App\OurEdu\Courses\Repository\CourseSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use Carbon\Carbon;
use App\OurEdu\Courses\Models\Course;

class CourseSessionController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;
    private VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository;

    public function __construct(
        CourseSessionRepositoryInterface $sessionRepositoy,
        VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository,

    ) {
        $this->module = 'courseSessions';
        $this->repository = $sessionRepositoy;

        $this->title = trans('course_sessions.Course Sessions');
        $this->parent = ParentEnum::ADMIN;
        $this->VCRSessionParticipantsRepository = $VCRSessionParticipantsRepository;

    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.courses.get.course.sessions', $data['row']->course_id)];

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function putEdit(CourseSessionRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);
        if (Carbon::parse($row->date . ' ' . $row->start_time)
            ->lessThanOrEqualTo(Carbon::now())) {
            $request['date'] = $row->date;
            $request['start_time'] = $row->start_time;
            $request['end_time'] = $row->end_time;
        }
        if ($this->repository->setSession($row)
            ->update($request->all())) {

            flash()->success(trans('app.Update successfully'));

            $row->VCRSession()->update([
                'time_to_start'=>date('Y-m-d H:i:s', strtotime("{$row->date} {$request->start_time}")),
                'time_to_end'=> date('Y-m-d H:i:s', strtotime("{$row->date} {$request->end_time}")),
            ]);

            event(new CourseSessionUpdated($row));
            $this->notifyStudent($row->VCRSession);
            return redirect()->route('admin.courses.get.course.sessions', $row->course_id);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.courses.get.course.sessions', $data['row']->course_id)];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function cancel($id)
    {
        $row = $this->repository->findOrFail($id);

        if ($row->status == CourseSessionEnums::ACTIVE) {
            $this->repository->setSession($row)
                ->update(['status' => CourseSessionEnums::CANCELED]);
            flash()->success(trans('app.Canceled successfully'));

            event(new CourseSessionCanceled($row));

            return redirect()->route('admin.courses.get.course.sessions', $row->course_id);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
    private function notifyStudent(VCRSession $VCRSession)
    {
        if ((new Carbon($VCRSession->time_to_start))->gt(now()->addMinutes(15))) {
            return ;
        }

        $toBeNotifiedStudents = $this->VCRSessionParticipantsRepository
            ->getSessionStudentParticipants($VCRSession->id);

        NotificationStudentsJob::dispatch($toBeNotifiedStudents, $VCRSession, true)
            ->delay((new Carbon($VCRSession->time_to_start)))
            ->onQueue('sessions');

        $VCRSession->update(['is_notified'=>1]);
    }

}
