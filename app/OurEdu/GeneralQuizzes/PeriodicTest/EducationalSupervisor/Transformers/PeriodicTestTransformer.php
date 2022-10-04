<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Classroom\Transformers\StudentTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HwStudentTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
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
        "subject"
    ];
    protected array $availableIncludes = [
        "students",
        "classroomStudents",
        "hwStudents",
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
            'grade_class' => (string)$periodicTest->gradeClass->title,
            'subject' => (string)$periodicTest->subject->name,
        ];
        return $transformedData;
    }

    public function includeActions(GeneralQuiz $periodicTest)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.educational-supervisor.getView',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('general_quizzes.view Periodic Test'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_PERIODIC_TEST
        ];
        if ($periodicTest->is_active) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.periodic-test.educational-supervisor.postDeactivate',
                    ['periodicTest' => $periodicTest->id]
                ),
                'label' => trans('general_quizzes.Deactivate Periodic Test'),
                'method' => 'POST',
                'key' => APIActionsEnums::DEACTIVATE_PERIODIC_TEST
            ];
        }


        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.educational-supervisor.getQuestionsList',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('general_quizzes.List Question'),
            'method' => 'GET',
            'key' => APIActionsEnums::List_PERIODIC_TEST_QUESTION
        ];
        if (is_null($periodicTest->published_at)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.periodic-test.educational-supervisor.putEdit',
                    ['periodicTest' => $periodicTest->id]
                ),
                'label' => trans('general_quizzes.edit Periodic Test'),
                'method' => 'GET',
                'key' => APIActionsEnums::EDIT_PERIODIC_TEST
            ];
        }
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.educational-supervisor.deleteDelete',
                ['periodicTest' => $periodicTest->id]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_PERIODIC_TEST
        ];
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeClassrooms(GeneralQuiz $periodicTest)
    {
        $classrooms = $periodicTest->classrooms;
        return $this->collection($classrooms, new ClassroomTransformer(), ResourceTypesEnums::CLASSROOM);
    }


    public function includeStudents(GeneralQuiz $periodicTest)
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

    public function includeHwStudents(GeneralQuiz $periodicTest)
    {
        if ($periodicTest->studentsAnswered()->count()) {
            $students = $periodicTest->studentsAnswered;

            return $this->collection($students, new HwStudentTransformer($periodicTest), ResourceTypesEnums::STUDENT);
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

    public function includeSubject(GeneralQuiz $periodicTest)
    {
        $subject = $periodicTest->subject;
        return $this->item(
            $subject,
            new \App\OurEdu\GeneralQuizzes\Lookup\Transformers\SubjectLookUpTransformer(),
            ResourceTypesEnums::SUBJECT
        );
    }

    public function getPeriodicTestAvg(GeneralQuiz $periodicTest)
    {
        $studentsGradsQry = $periodicTest->studentsAnswered;
        $studentsGradsAvg = 0;
        if ($studentsGradsQry->count() > 0) {
            $studentsGradsAvg = $studentsGradsQry->sum('score_percentage') / $studentsGradsQry->count();
        }
        return $studentsGradsAvg;
    }

    public function includeGradeClass(GeneralQuiz $homework)
    {
        return $this->item($homework->gradeClass, new GradeClassLookUpTransformer(), ResourceTypesEnums::GRADE_CLASS);
    }
}
