<?php

namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use OwenIt\Auditing\Models\Audit;

class LiveSessionRepository implements LiveSessionRepositoryInterface
{
    private $LiveSession;

    public function __construct(LiveSession $LiveSession)
    {
        $this->LiveSession = $LiveSession;
    }

    public function setLiveSession($LiveSession)
    {
        $this->LiveSession = $LiveSession;

        return $this;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return $this->LiveSession->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->LiveSession->latest()->with('sessions')->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->LiveSession->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param array $data
     * @return LiveSession
     */
    public function create(array $data): LiveSession
    {
        return $this->LiveSession->create($data);
    }

    /**
     * @param array $data
     * @return LiveSession|null
     */
    public function update(array $data): ?LiveSession
    {
        if ($this->LiveSession->update($data)) {
            $session = $this->LiveSession->session ?? null;

            if ($session) {
                $session->update($data);
            }

            return $this->LiveSession->find($this->LiveSession->id);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->LiveSession->delete();
    }

    public function findOrFail($id): ?LiveSession
    {
        return LiveSession::findOrFail($id);
    }

    /**
     * @param  Student  $student
     * @return LengthAwarePaginator
     */
    public function getRelatedLiveSessionsForStudent(Student $student): LengthAwarePaginator
    {
        $subjects = Subject::where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id',auth()->user()->country_id)
            ->where('grade_class_id',$student->class_id)
            ->pluck('id')->toArray();

        return $this->LiveSession->latest()
            ->where(function ($query) use ($subjects){
                $query->whereIn('subject_id',$subjects);
                $query->orWhereNull('subject_id');
            })
            ->where('is_active',1)
            ->whereHas('session', function ($q){
                $q->whereDate('date', '>=', date('Y-m-d'));
                $q->where('status','!=',CourseSessionEnums::CANCELED);
//                    ->wherebetween('start_time', [
//                                now()->format('H:i:s'),
//                                now()->addMinutes(CourseSessionEnums::AVAILABILITY_TIME)->format('H:i:s')
//                ])
                ;
            })
            ->with('session', 'instructor', 'subject')
            ->jsonPaginate();
    }

    /**
     * function add start and end time of session to live session log
     * @param LiveSession $LiveSession
     * @return void
     */
    public function addSessionTimeToLog(LiveSession $LiveSession): void
    {
        $auditRow = Audit::where('auditable_type', LiveSession::class)
            ->where('auditable_id', $LiveSession->id)->where('event', 'created')
            ->first();
        $auditRowData = $auditRow->new_values;
        $session = $LiveSession->sessions()->first()->toArray();
        $auditRowData['start_time'] = $session['start_time'] ;
        $auditRowData['end_time'] = $session['end_time'] ;
        $auditRowData['date'] = $session['date'] ;
        $auditRow->update(['new_values' => $auditRowData]);
    }


    public function getRelatedLiveSessionsForInstructor(Instructor $instructor): LengthAwarePaginator {

        return $this->LiveSession->where('instructor_id' , $instructor->id)
            ->orderBy('id' , 'desc')
            ->with('session' , 'subject')
            ->jsonPaginate();
    }

}
