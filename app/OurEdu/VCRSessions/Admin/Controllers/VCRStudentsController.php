<?php

    namespace App\OurEdu\VCRSessions\Admin\Controllers;

    use App\OurEdu\BaseApp\Controllers\BaseController;
    use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Subjects\Models\Subject;
    use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRSession;
    use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;
    use App\OurEdu\VCRSessions\Admin\Exports\SubjectsVCRSessionsExport;
    use App\OurEdu\VCRSessions\Admin\Exports\SubjectVCRSessionsPresenceExport;
    use Maatwebsite\Excel\Facades\Excel;

    class VCRStudentsController extends  BaseController
    {
        private $module,
         $title,
         $parent;
        private $vcrScheduleRepository;

        /**
         * @var SubjectRepositoryInterface
         */
        private $subjectRepository;


        public function __construct(
            SubjectRepositoryInterface $subjectRepository,
            VCRScheduleRepositoryInterface $vcrScheduleRepository
        ){
            $this->module = 'VCRSessions';
            $this->title = trans('VCRSessions.VCRStudents');
            $this->parent = ParentEnum::ADMIN;
            $this->subjectRepository = $subjectRepository;
            $this->vcrScheduleRepository = $vcrScheduleRepository;
        }

        public function subjectVCR()
        {
            $data['page_title'] = $this->title;
            $data['breadcrumb'] = '';
            $data['rows'] = $this->subjectRepository->getSubjectWithFinishedVCRSessionsCount();

            return view($this->parent . '.' . $this->module . '.students.subjects-vcr-sessions', $data);
        }

        public function subjectVCRExport()
        {
            $subjects = $this->subjectRepository->getSubjectWithFinishedVCRSessionsCount();

            return Excel::download(new SubjectsVCRSessionsExport($subjects), "subjects_vcr_sessions_count.xls");
        }

        public function subjectVCRSchedules(Subject $subject)
        {
            $data['page_title'] = "$this->title: " . trans('app.vcr_schedule');
            $perPage = env('PAGE_LIMIT', 20);
            $data['rows'] = $subject->VCRSchedules()->paginate($perPage);

            return view($this->parent . '.' . $this->module . '.students.vcr-schedule', $data);
        }

        public function subjectLiveSessions(Subject $subject)
        {
            $data['page_title'] = "$this->title: " . trans('app.Live Sessions');
            $perPage = env('PAGE_LIMIT', 20);
            $data['rows'] = $subject->liveSessions()
                ->with('instructor:id,first_name,last_name')
                ->withCount('VCRSessionPresence')
                ->paginate($perPage);

            return view($this->parent . '.' . $this->module . '.students.live-sessions', $data);
        }

        public function subjectCourses(Subject $subject)
        {
            $data['page_title'] = "$this->title: " . trans('app.Courses');
            $perPage = env('PAGE_LIMIT', 20);
            $data['rows'] = $subject->courses()
                ->with('instructor:id,first_name,last_name')
                ->paginate($perPage);

            return view($this->parent . '.' . $this->module . '.students.courses', $data);
        }

        public function courseVCRAttendance(Course $course)
        {
            $data['page_title'] = $this->title;
            $data['parent'] = $course;
            $data['rows'] = VCRSession::query()
                ->with("instructor")
                ->withCount("VCRSessionPresence")
                ->where("course_id", "=", $course->id)
                ->where('time_to_end',"<", now())
                ->orderByDesc('started_at')
                ->paginate(env("PAGE_LIMIT", 15));

            return view($this->parent . '.' . $this->module . '.students.VcrStudent', $data);
        }

        public function courseVCRAttendanceExport(Course $course)
        {
            $VCRSessions = VCRSession::query()
                ->with("instructor")
                ->withCount("VCRSessionPresence")
                ->where("course_id", "=", $course->id)
                ->where('time_to_end',"<", now())
                ->paginate(env("PAGE_LIMIT", 15));

            return Excel::download(new SubjectVCRSessionsPresenceExport($VCRSessions), $this->title . "_" . $course->name . "_.xls");
        }

        public function scheduleVCRAttendance(VCRSchedule $vcrSchedule)
        {
            $data['page_title'] = $this->title;
            $data['parent'] = $vcrSchedule;
            $data['rows'] = $vcrSchedule->vcrSessions()
                ->with("instructor")
                ->withCount("VCRSessionPresence")
                ->orderBy('time_to_start')
                ->paginate(env("PAGE_LIMIT", 15));

            return view($this->parent . '.' . $this->module . '.students.VcrStudent', $data);
        }

    }
