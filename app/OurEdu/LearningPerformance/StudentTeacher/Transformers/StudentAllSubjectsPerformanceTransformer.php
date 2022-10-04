<?php


namespace App\OurEdu\LearningPerformance\StudentTeacher\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentAllSubjectsPerformanceTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
        'activityLog',
        'subjects'
    ];

    private $subjects;

    public function transform(LearningPerformance $learningPerformance)
    {
        $this->setSubjects($learningPerformance);

        return [
            'id' => Str::uuid(),
            'activity_pagination' =>  $learningPerformance->student->events()
                ->when(request('date_from'), function ($q)  {
                    $q ->where('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->where('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->whereIn('event_properties->subject_attributes->subject_id',
                    $learningPerformance->student->subjects()->pluck('subject_id')->toArray())
                ->latest()->paginate(10, ['id'], 'activity-page')
        ];
    }

    public function includeActivityLog(LearningPerformance $learningPerformance)
    {
        $activities = $learningPerformance->student->events()
            ->when(request('date_from'), function ($q)  {
                $q ->where('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->where('created_at' , '<=' , endOfDay(request('date_to')));
            })
            ->whereIn('event_properties->subject_attributes->subject_id',
                $learningPerformance->student->subjects()->pluck('subject_id')->toArray())

            ->latest()
            ->paginate(10, ['*'], 'activity-page');
        if (count($activities)) {
            return $this->collection($activities, new ActivitiesLogTransformer(), ResourceTypesEnums::Exam);
        }
    }

    public function includeSubjects(LearningPerformance $learningPerformance)
    {
        if (count($this->subjects)) {
            return $this->collection($this->subjects, new SubjectsListTransformer($learningPerformance->student), ResourceTypesEnums::SUBJECT);
        }
    }

    public function setSubjects(LearningPerformance $learningPerformance) {
        $student = $learningPerformance->student;
        $studentTeacher = auth()->user();
        $studentSentInvitationToTeacher = Invitation::where('sender_id' , $student->user->id)
            ->where('invitable_type' , User::class)
            ->where('invitable_id' , $studentTeacher->id)
            ->exists();

        if ($studentSentInvitationToTeacher){
            $this->subjects = $studentTeacher
                ->studentTeacherSubjects()
                ->where('student_id', $student->user->id)
                ->where('status', InvitationEnums::ACCEPTED)
                ->subjects;
        } else {
            $this->subjects = $student->subjects;
        }
    }
}
