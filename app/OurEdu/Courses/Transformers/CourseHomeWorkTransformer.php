<?php


namespace App\OurEdu\Courses\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Classroom\Transformers\StudentTransformer;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizStatusEnum;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HomeworkAllowedStudentsTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HwStudentTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\SubjectTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GradeClasses\Transformers\GradeClassLookUpTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\ClassroomTransformer;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class CourseHomeWorkTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];


    public function transform($homework)
    {
       
        $transformerDatat = [
            'id' => (int)$homework->id,
            'title' => (string)$homework->title,
            'start_at' => (string)$homework->start_at,
            'end_at' => (string)$homework->end_at,
            'status' => (string)$this->getHomeworkStatus($homework),
        ];


        return $transformerDatat;
    }


    private function getHomeworkStatus($homework)
    {
        // if student started the homework
        if ($studentHomework = Auth::guard('api')->user()->schoolStudentGeneralQuizzes()
                ->where('general_quiz_id', $homework->id)
                ->first()
            ) {
                if (!$studentHomework->is_finished && is_null($studentHomework->finished_time)) {
                    return GeneralQuizStatusEnum::STARTED;
                }
                return GeneralQuizStatusEnum::FINISHED;
        } else {
            return GeneralQuizStatusEnum::NOT_STARTED;
        }
    }

    public function includeActions($homework)
    {
        $actions = [];
        if ($user = Auth::guard('api')->user()) {
            if (! GeneralQuizStudent::where([
                'student_id'    =>  $user->id,
                'general_quiz_id'    =>  $homework->id,
            ])->exists() && $user->type == UserEnums::STUDENT_TYPE) {
                
                //Start Homework
                $actions[] = [
                'endpoint_url' => buildScopeRoute('api.general-quizzes.homework.student.post.startHomework', ['homeworkId' => $homework->id]),
                'label' => trans('general_quizzes.Start Homework'),
                'method' => 'POST',
                'key' => APIActionsEnums::START_HOMEWORK
            ];
            }
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

}
