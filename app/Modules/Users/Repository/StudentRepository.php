<?php


namespace App\Modules\Users\Repository;

use App\Modules\Courses\Enums\CourseEnums;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\SubModels\CourseSubscribe;
use App\Modules\SubjectPackages\SubscribedPackage;
use App\Modules\Subjects\Models\Subject;
use App\Modules\Subscribes\Subscribe;
use App\Modules\Subscribes\SubscribeCourse;
use App\Modules\Users\Models\Student;
use App\Modules\Users\User;
use App\Modules\VCRSchedules\Models\LiveSessionParticipant;
use App\Modules\VCRSchedules\Models\VCRSession;
use App\Modules\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Mpdf\Tag\Sub;

class StudentRepository implements StudentRepositoryInterface
{
    public function create(array $data): ?Student
    {
        return Student::create($data);
    }

    public function findOrFail(int $id): ?Student
    {
        return Student::findOrFail($id);
    }

    public function update(Student $Student, array $data): bool
    {
        return $Student->update($data);
    }

    public function delete(Student $Student): bool
    {
        return $Student->delete();
    }

    public function getStudentByUserId(int $userId): ?Student
    {
        return Student::where('user_id', $userId)->firstOrFail();
    }

    /**
     * @param  Student  $student
     * @param  array  $data
     * @return Subscribe|null
     */
    public function createSubscribe(Student $student, array $data): ?Subscribe
    {
        return $student->subscribe()->create($data);
    }

    /**
     * @param  Student  $student
     * @param  array  $data
     * @return SubscribeCourse|null
     */
    public function createCourseSubscribe(Student $student, array $data): ?SubscribeCourse
    {
        return $student->subscribeCourse()->create($data);
    }


    public function subscripeOnCourse(Student $student, Course $course)
    {
        return $student->courses()->sync([$course->id => ['date_of_pruchase' => now()]]);
    }

    public function subscribePackage(Student $student, array $data) : ?SubscribedPackage
    {
        return $student->subscribedPackages()->create($data);
    }

    public function getClassroomStudentsByUserIds($userIds,$classroomIds=[]){
        return Student::query()
                ->whereIn('user_id',$userIds)
                ->whereHas('classroom',function($query) use($classroomIds){
                    if(count($classroomIds)>0){
                        $query->whereIn('id',$classroomIds);
                    }
                })->pluck('user_id')->toArray();
    }

    public function getStudentCourseSessions(User $user, Subject $subject = null)
    {
        $sessions = VCRSession::whereHas('participants', function ($participent) use ($user) {
            $participent->where('user_id', $user->id);
        })->where('vcr_session_type', VCRSessionsTypeEnum::COURSE_SESSION);
        if (!is_null($subject)) {
            $sessions = $sessions->where('subject_id', $subject->id);
        }
        $sessions = $sessions->whereHas('course', function ($course) use ($user) {
                $course->where('type', CourseEnums::SUBJECT_COURSE);
            })->count();

        return $sessions;
    }

    public function getStudentLiveSessions(User $user, Subject $subject = null)
    {
        $sessions = VCRSession::whereHas('participants', function ($participent) use ($user) {
            $participent->where('user_id', $user->id);
        })->where('vcr_session_type', VCRSessionsTypeEnum::LIVE_SESSION);
        if (!is_null($subject)) {
            $sessions = $sessions->where('subject_id', $subject->id);
        }
        $sessions = $sessions->WhereHas('liveSession')->count();

        return $sessions;
    }

    public function getStudentRequestedSessions(User $user, Subject $subject = null)
    {
        $sessions = VCRSession::whereHas('participants', function ($participent) use ($user) {
            $participent->where('user_id', $user->id);
        })->where('vcr_session_type', VCRSessionsTypeEnum::VCR_SCHEDULE_SESSION);
        if (!is_null($subject)) {
            $sessions = $sessions->where('subject_id', $subject->id);
        }
        $sessions = $sessions->WhereHas('workingDay')->count();

        return $sessions;
    }

    public function getStudentCourseAttendance(Student $student, Subject $subject = null)
    {
        $courses = $student->courses()->where('type', CourseEnums::SUBJECT_COURSE);
        if (!is_null($subject)) {
            $courses = $courses->where('subject_id', $subject->id);
        }
        $courses = $courses->withCount('sessions')
            ->withCount(['VCRSession' => function ($vcr) use ($student) {
                $vcr->whereHas('participants', function ($participant) use ($student) {
                    $participant->where('user_id', $student->user->id);
                });
            }])->get();

        return $courses->unique();

    }

    public function getStudentByClassRoom(array $classrooms){
       return Student::whereIn('classroom_id', $classrooms)
         ->pluck('id')->toArray();
    }
}
