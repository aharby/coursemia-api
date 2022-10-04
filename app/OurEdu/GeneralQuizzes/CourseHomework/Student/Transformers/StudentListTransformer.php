<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Student\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;

class StudentListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    public function transform($courseHomework)
    {
        $transformerDatat = [
            'id' => (int)$courseHomework->id,
            'title' => (string)$courseHomework->title,
            'start_at' => (string)$courseHomework->start_at,
            'end_at' => (string)$courseHomework->end_at,
            'score' => (string)$this->getStudentHomeWorkScore($courseHomework),
            'mark' => (float)$courseHomework->mark,
        ];


        return $transformerDatat;
    }

    public function includeActions($courseHomework)
    {
        $studentAnswers = $courseHomework->quizStudentAnswers()
               ->where('student_id', auth()->id())->count();

        $actions = [];

        if ($studentAnswers > 0) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.general-quizzes.course-homework.student.get.student.answers', [
                    'courseHomework' => $courseHomework->id
                ]),
                'label' => trans('general_quizzes.view_student_answers'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_STUDENT_ANSWERS
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.general-quizzes.homework.student.post.feedback', ['homework' => $courseHomework->id]),
                'label' => trans('general_quizzes.Fedback'),
                'method' => 'GET',
                'key' => APIActionsEnums::FEDBACK
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function getStudentHomeWorkScore(GeneralQuiz $courseHomework)
    {
        $studentScore = $courseHomework->quizStudentAnswers()
              ->where('is_correct', 1)
              ->where('student_id', auth()->id())
               ->get()->sum('score');

        $studentsGradsAvg = 0;
        $totalGrade = $courseHomework->questions()->pluck('grade')->sum();
        if ($totalGrade > 0 ) {
            $studentsGradsAvg = $studentScore  . '/' . $totalGrade;
        }
        return $studentsGradsAvg;
    }
}
