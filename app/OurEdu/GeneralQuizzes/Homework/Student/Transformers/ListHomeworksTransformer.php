<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Student\Transformers;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralQuizzes\Model\GeneralQuiz;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\GeneralQuizzes\Subject\Transformers\SubjectTransformer;
use App\OurEdu\GeneralQuizzes\Subject\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizStatusEnum;
class ListHomeworksTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'subject',
        'sections',
        'actions',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->student = Auth::guard('api')->user();
    }

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
        if ($studentHomework = $this->student->schoolStudentGeneralQuizzes()
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
            ])->exists()) {
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

    public function includeSubject($homework)
    {
        $subject = $homework->subject;

        return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
    }

    public function includeSections($homework)
    {
        $subjectSections = $homework->sections;
        return $this->collection($subjectSections, new SubjectFormatSubjectTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }
}
