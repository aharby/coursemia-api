<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HomeworkAllowedStudentsTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HwStudentTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class CourseHomeWorkTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
        "students",
        "hwStudents",
        'course'
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralQuiz $homework)
    {
        $transformedData = [
            'id' => (int)$homework->id,
            'title' => (string)$homework->title,
            'random_question' => (bool)$homework->random_question,
            'quiz_type' => (string)$homework->quiz_type,
            'start_at' => (string)$homework->start_at,
            'end_at' => (string)$homework->end_at,
            'is_published' => (bool)!is_null($homework->published_at),
            'published_at' => (string)$homework->published_at,
            'avg' => (float)$this->getHomeWorkAvg($homework),
            'score' => (string)$this->getStudentHomeWorkScore($homework),
            'mark' => (float)$homework->mark,
        ];
        if (isset($this->params['listScore']) && isset($this->params['students'])) {
            $students = $this->params['students'];
            $transformedData['pagination'] = (object)[
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'current_page' => $students->currentPage(),
                'count' => $students->count(),
                'total_pages' => $students->lastPage(),
                'next_page' => $students->nextPageUrl(),
                'previous_page' => $students->previousPageUrl()
            ];
        }

        return $transformedData;
    }

    public function includeActions(GeneralQuiz $homework)
    {

        if (!$homework->published_at) {

            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.post.publish_course_homework',
                    ['courseHomework' => $homework->id]
                ),
                'label' => trans('app.Publish'),
                'method' => 'POST',
                'key' => APIActionsEnums::PUBLISH_HOMEWORK
            ];

        } else {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.get.list_students_scores',
                    ['courseHomework' => $homework->id]
                ),
                'label' => trans('app.list students scores'),
                'method' => 'GET',
                'key' => APIActionsEnums::lIST_STUDENT_SCORES
            ];

        }

        if (isset($this->params['listScore']) && isset($this->params['students'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.get.export_students_scores',
                    ['courseHomework' => $homework->id]
                ),
                'label' => trans('app.export students scores'),
                'method' => 'GET',
                'key' => APIActionsEnums::EXPORT_STUDENT_SCORES
            ];
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.retake_course_homework',
                    ['courseHomework' => $homework->id]
                ),
                'label' => trans('app.Retake Homework'),
                'method' => 'GET',
                'key' => APIActionsEnums::RETAKE_QUIZ
            ];
        }

        if (!$homework->studentsAnswered->count()) {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.course-homework.instructor.put.edit_course_homework',
                [
                    'courseHomework' => $homework->id,
                    'course' => $homework->course?->id]
            ),
            'label' => trans('general_quizzes.edit Homework'),
            'method' => 'PUT',
            'key' => APIActionsEnums::EDIT_HOMEWORK
        ];
    }

    $actions[] = [
        'endpoint_url' => buildScopeRoute(
            'api.general-quizzes.course-homework.instructor.post.create_course_homework_question',
            ['courseHomework' => $homework->id]
        ),
        'label' => trans('general_quizzes.add question'),
        'method' => 'POST',
        'key' => APIActionsEnums::ADD_QUESTION
    ];
    
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.course-homework.instructor.get.view_course_homework',
                ['courseHomework' => $homework->id]
            ),
            'label' => trans('general_quizzes.view Homework'),
            'method' => 'PUT',
            'key' => APIActionsEnums::VIEW_HOMEWORK
        ];
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.course-homework.instructor.get.preview_course_homework',
                ['courseHomework' => $homework->id]
            ),
            'label' => trans('general_quizzes.view as student'),
            'method' => 'PUT',
            'key' => APIActionsEnums::PREVIEW
        ];

        if (!$homework->studentsAnswered->count()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.delete.course_homework',
                    ['courseHomework' => $homework->id]
                ),
                'label' => trans('app.Delete'),
                'method' => 'DELETE',
                'key' => APIActionsEnums::DELETE_HOMEWORK
            ];
        }


        if ($homework->questions->count()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.get.course_homework_questions',
                    ['courseHomework' => $homework->id]
                ),
                'label' => trans('general_quizzes.questions'),
                'method' => 'GET',
                'key' => APIActionsEnums::List_HOMEWORK_QUESTION
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.export.grades',
                    ['courseHomework' => $homework->id]
                ),
                'label' => trans('app.Export by questions grades'),
                'method' => 'GET',
                'key' => APIActionsEnums::EXPORT_BY_QUESTIONS_GRADES
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeStudents($homework)
    {
        if ($homework->classrooms()->count() == 1 && $homework->students()->count()) {
            $students = $homework->students()->with('student')->get();
            return $this->collection($students, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }


    public function includeHwStudents($homework)
    {
        $studentsAnswered = $homework->studentsAnswered()->with('user.student');
        if (isset($this->params['students'])) {
            $students = $this->params['students'];
            $studentsAnswered = $studentsAnswered->pluck('score', 'student_id')->toArray();
            return $this->collection(
                $students,
                new HomeworkAllowedStudentsTransformer($homework, $studentsAnswered),
                ResourceTypesEnums::STUDENT
            );
        } else {
            if ($homework->studentsAnswered()->count()) {
                $students = $studentsAnswered->get();
                return $this->collection($students, new HwStudentTransformer($homework), ResourceTypesEnums::STUDENT);
            }
        }
    }

    public function getHomeWorkAvg(GeneralQuiz $homework)
    {
        $studentsGradsQry = $homework->studentsAnswered->where('is_finished', '=', 1);
        $studentsGradsAvg = 0;
        if ($studentsGradsQry->count() > 0) {
            $studentsGradsAvg = $studentsGradsQry->sum('score_percentage') / $studentsGradsQry->count();
        }
        return $studentsGradsAvg;
    }

    public function getStudentHomeWorkScore(GeneralQuiz $homework)
    {
        $studentScore = $homework->quizStudentAnswers()->where('is_correct', 1)->get()->sum('score');
        $studentAnsweredCount = $homework->studentsAnswered()->count();
        $studentsGradsAvg = 0;
        $totalGrade = $homework->questions()->pluck('grade')->sum();
        if ($totalGrade > 0 && $studentAnsweredCount > 0) {
            $studentsGradsAvg = round($studentScore / $studentAnsweredCount, 2) . '/' . $totalGrade;
        }
        return $studentsGradsAvg;
    }

    public function includeCourse(GeneralQuiz $homework)
    {
        if ($homework->course) {
            return $this->item($homework->course, new CourseTransformer(), ResourceTypesEnums::COURSE);
        }
    }
}
