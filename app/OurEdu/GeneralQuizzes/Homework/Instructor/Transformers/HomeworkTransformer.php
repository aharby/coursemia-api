<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Classroom\Transformers\StudentTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GradeClasses\Transformers\GradeClassLookUpTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\ClassroomTransformer;
use App\OurEdu\Users\Transformers\UserTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class HomeworkTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        "classrooms",
        "sections",
    ];
    protected array $availableIncludes = [
        "students",
        "classroomStudents",
        "hwStudents",
        "subject",
        "gradeClass"
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
            'branch_id' => (int)$homework->branch_id,
            'subject_id' => (int)$homework->subject_id,
            'grade_class_id' => (int)$homework->grade_class_id,
            'school_account_id' => (int)$homework->school_account_id,
            'avg' => (float)$this->getHomeWorkAvg($homework),
            'score' => (string)$this->getStudentHomeWorkScore($homework),
            'mark' => (float)$homework->mark,
            'is_repeated' => (bool)$homework->is_repeated,
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
                    'api.general-quizzes.homework.instructor.post.publish',
                    ['homework' => $homework->id]
                ),
                'label' => trans('app.Publish'),
                'method' => 'POST',
                'key' => APIActionsEnums::PUBLISH_HOMEWORK
            ];
        }

        if (!empty($homework->published_at) and $homework->end_at < Carbon::now(
            ) and !$homework->is_repeated and $homework->is_active) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.homework.instructor.retake',
                    ['homework' => $homework->id]
                ),
                'label' => trans('general_quizzes.retake'),
                'method' => 'POST',
                'key' => APIActionsEnums::VIEW_HOMEWORK
            ];
        }
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.put.edit',
                ['homeworkId' => $homework->id]
            ),
            'label' => trans('general_quizzes.edit Homework'),
            'method' => 'PUT',
            'key' => APIActionsEnums::EDIT_HOMEWORK
        ];
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.delete.delete',
                ['homework' => $homework->id]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_HOMEWORK
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.get.view',
                ['homework' => $homework->id]
            ),
            'label' => trans('general_quizzes.view Homework'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_HOMEWORK
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.get.questions_list',
                ['homework' => $homework->id]
            ),
            'label' => trans('general_quizzes.List Question'),
            'method' => 'GET',
            'key' => APIActionsEnums::List_HOMEWORK_QUESTION
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.get.preview',
                ['homework' => $homework->id]
            ),
            'label' => trans('general_quizzes.preview'),
            'method' => 'GET',
            'key' => APIActionsEnums::INSTRUCTOR_PREVIEW_HOMEWORK_AS_STUDENT
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.grades.export',
                ['homework' => $homework->id]
            ),
            'label' => trans('app.Export by questions grades'),
            'method' => 'GET',
            'key' => APIActionsEnums::EXPORT_BY_QUESTIONS_GRADES
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeClassrooms($homework)
    {
        $classrooms = $homework->classrooms;
        return $this->collection($classrooms, new ClassroomTransformer(), ResourceTypesEnums::CLASSROOM);
    }


    public function includeStudents($homework)
    {
        if ($homework->classrooms()->count() == 1 && $homework->students()->count()) {
            $students = $homework->students()->with('student')->get();
            return $this->collection($students, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeClassroomStudents(GeneralQuiz $homework)
    {
        if ($homework->classrooms()->count() == 1 && $homework->students()->count()) {
            $students = $homework->classrooms()->first()->students;

            return $this->collection($students, new StudentTransformer($homework), ResourceTypesEnums::STUDENT);
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

    public function includeSections(GeneralQuiz $homework)
    {
        $sections = $homework->sectionsRelations ?? [];

        return $this->collection(
            $sections,
            new SubjectFormatSubjectTransformer(),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
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

    public function includeSubject(GeneralQuiz $homework)
    {
        return $this->item($homework->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
    }

    public function includeGradeClass(GeneralQuiz $homework)
    {
        return $this->item($homework->gradeClass, new GradeClassLookUpTransformer(), ResourceTypesEnums::GRADE_CLASS);
    }
}
