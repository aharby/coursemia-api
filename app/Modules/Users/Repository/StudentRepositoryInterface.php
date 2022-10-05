<?php


namespace App\Modules\Users\Repository;


use App\Modules\Courses\Models\SubModels\CourseSubscribe;
use App\Modules\Subjects\Models\Subject;
use App\Modules\Subscribes\Subscribe;
use App\Modules\Subscribes\SubscribeCourse;
use App\Modules\Users\Models\Student;
use App\Modules\Users\User;
use App\Modules\VCRSchedules\Models\LiveSessionParticipant;

interface StudentRepositoryInterface
{
    public function create(array $data): ?Student;

    public function findOrFail(int $id): ?Student;

    public function update(Student $student, array $data): bool;

    public function delete(Student $student): bool;

    public function getStudentByUserId(int $userId): ?Student;

    /**
     * @param Student $student
     * @param array $data
     * @return Subscribe|null
     */
    public function createSubscribe(Student $student, array $data): ?Subscribe;

    /**
     * @param Student $student
     * @param array $data
     * @return SubscribeCourse|null
     */
    public function createCourseSubscribe(Student $student, array $data): ?SubscribeCourse;

    public function getClassroomStudentsByUserIds($userIds,$classroomIds=[]);

    public function getStudentCourseSessions(User $user, Subject $subject = null);
    public function getStudentLiveSessions(User $user, Subject $subject = null);
    public function getStudentRequestedSessions(User $user, Subject $subject = null);
    public function getStudentCourseAttendance(Student $student, Subject $subject = null);
    public function getStudentByClassRoom(array $classrooms);

}
