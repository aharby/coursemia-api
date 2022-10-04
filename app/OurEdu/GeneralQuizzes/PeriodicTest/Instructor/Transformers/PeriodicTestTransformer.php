<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Classroom\Transformers\StudentTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GradeClasses\Transformers\GradeClassLookUpTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\ClassroomTransformer;
use App\OurEdu\Users\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class PeriodicTestTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        "classrooms",
        "sections",
    ];
    protected array $availableIncludes = [
        "students",
        "classroomStudents",
        "periodicTestStudents",
        "subject",
        "gradeClass"
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralQuiz $periodicTest)
    {
        $transformedData = [
            'id' => (int)$periodicTest->id,
            'title' => (string)$periodicTest->title,
            'random_question' => (bool)$periodicTest->random_question,
            'quiz_type' => (string)$periodicTest->quiz_type,
            'start_at' => (string)$periodicTest->start_at,
            'end_at' => (string)$periodicTest->end_at,
            'is_published' => (bool)!is_null($periodicTest->published_at),
            'published_at' => (string)$periodicTest->published_at,
            'branch_id' => (int)$periodicTest->branch_id,
            'subject_id' => (int)$periodicTest->subject_id,
            'grade_class_id' => (int)$periodicTest->grade_class_id,
            'school_account_id' => (int)$periodicTest->school_account_id,
            'avg' => (float)$this->getPeriodicTestAvg($periodicTest),
            'mark' => (float)$periodicTest->mark,
            'is_repeated' => (bool)$periodicTest->is_repeated,
            'score' => (string)$this->getStudentPeriodicTestScore($periodicTest),
            'test_time' => (float)$periodicTest->test_time / 60
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

    public function includeActions(GeneralQuiz $periodicTest)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.instructors.get.view',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('general_quizzes.view Periodic Test'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_PERIODIC_TEST
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.instructors.get.questions_list',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('general_quizzes.List Question'),
            'method' => 'GET',
            'key' => APIActionsEnums::List_PERIODIC_TEST_QUESTION
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.instructors.get.preview',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('general_quizzes.preview'),
            'method' => 'GET',
            'key' => APIActionsEnums::INSTRUCTOR_PREVIEW_PERIODIC_TEST_AS_STUDENT
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.instructors.grades.export',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('app.Export by questions grades'),
            'method' => 'GET',
            'key' => APIActionsEnums::EXPORT_BY_QUESTIONS_GRADES
        ];
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.instructors.delete.delete',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_PERIODIC_TEST
        ];
        if (!$periodicTest->published_at) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.periodic-test.instructors.post.publish',
                    ['periodicTest' => $periodicTest->id]
                ),
                'label' => trans('app.Publish'),
                'method' => 'POST',
                'key' => APIActionsEnums::PUBLISH_PERIODIC_TEST
            ];
        }
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.instructors.put.edit',
                ['periodicTestId' => $periodicTest->id]
            ),
            'label' => trans('general_quizzes.edit Periodic Test'),
            'method' => 'PUT',
            'key' => APIActionsEnums::EDIT_PERIODIC_TEST
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeClassrooms($periodicTest)
    {
        $classrooms = $periodicTest->classrooms;
        return $this->collection($classrooms, new ClassroomTransformer(), ResourceTypesEnums::CLASSROOM);
    }


    public function includeStudents($periodicTest)
    {
        if ($periodicTest->classrooms()->count() == 1 && $periodicTest->students()->count()) {
            $students = $periodicTest->students()->with('student')->get();
            return $this->collection($students, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeClassroomStudents(GeneralQuiz $periodicTest)
    {
        if ($periodicTest->classrooms()->count() == 1 && $periodicTest->students()->count()) {
            $students = $periodicTest->classrooms()->first()->students;

            return $this->collection($students, new StudentTransformer($periodicTest), ResourceTypesEnums::STUDENT);
        }
    }

    public function includePeriodicTestStudents($periodicTest)
    {
        $studentsAnswered = $periodicTest->studentsAnswered()->with('user.student');
        if (isset($this->params['students'])) {
            $students = $this->params['students'];
            $studentsAnswered = $studentsAnswered->pluck('score', 'student_id')->toArray();
            return $this->collection(
                $students,
                new PeriodicTestAllowedStudentsTransformer($periodicTest, $studentsAnswered),
                ResourceTypesEnums::STUDENT
            );
        } else {
            if ($periodicTest->studentsAnswered()->count()) {
                $students = $studentsAnswered->get();
                return $this->collection(
                    $students,
                    new PeriodicTestStudentTransformer($periodicTest),
                    ResourceTypesEnums::STUDENT
                );
            }
        }
    }

    public function includeSections(GeneralQuiz $periodicTest)
    {
        $sections = $periodicTest->sectionsRelations ?? [];

        return $this->collection(
            $sections,
            new SubjectFormatSubjectTransformer(),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
    }

    public function getPeriodicTestAvg(GeneralQuiz $periodicTest)
    {
        $studentsGradsQry = $periodicTest->studentsAnswered->where('is_finished', '=', 1);
        $studentsGradsAvg = 0;
        if ($studentsGradsQry->count() > 0) {
            $studentsGradsAvg = $studentsGradsQry->sum('score_percentage') / $studentsGradsQry->count();
        }
        return $studentsGradsAvg;
    }

    public function getStudentPeriodicTestScore(GeneralQuiz $periodicTest)
    {
        $studentScore = $periodicTest->quizStudentAnswers()->where('is_correct', 1)->get()->sum('score');
        $studentAnsweredCount = $periodicTest->studentsAnswered()->count();
        $studentsGradsAvg = 0;
        $totalGrade = $periodicTest->questions()->pluck('grade')->sum();
        if ($totalGrade > 0 && $studentAnsweredCount > 0) {
            $studentsGradsAvg = round($studentScore / $studentAnsweredCount, 2) . '/' . $totalGrade;
        }
        return $studentsGradsAvg;
    }


    public function includeSubject(GeneralQuiz $periodicTest)
    {
        return $this->item($periodicTest->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
    }


    public function includeGradeClass(GeneralQuiz $periodicTest)
    {
        return $this->item(
            $periodicTest->gradeClass,
            new GradeClassLookUpTransformer(),
            ResourceTypesEnums::GRADE_CLASS
        );
    }
}
