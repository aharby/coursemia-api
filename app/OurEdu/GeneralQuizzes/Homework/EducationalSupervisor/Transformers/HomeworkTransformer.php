<?php


namespace App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Transformers;


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
        "gradeClass",
        "subject"
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
            'grade_class' => (string)$homework->gradeClass->title,
            'subject' => (string)$homework->subject->name,
            'school_account_id' => (int)$homework->school_account_id,
            'avg' => (float)$this->getHomeWorkAvg($homework),
            'mark' => (float)$homework->mark,
        ];
        return $transformedData;
    }

    public function includeActions(GeneralQuiz $homework)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.educational-supervisor.get.view',
                ['homework' => $homework->id]
            ),
            'label' => trans('general_quizzes.view Homework'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_HOMEWORK
        ];

        if ($homework->is_active) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.homework.educational-supervisor.post.deactivate',
                    ['homework' => $homework->id]
                ),
                'label' => trans('general_quizzes.Deactivate Homework'),
                'method' => 'POST',
                'key' => APIActionsEnums::DEACTIVATE_HOMEWORK
            ];
        }


        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.educational-supervisor.get.questions_list',
                ['homework' => $homework->id]
            ),
            'label' => trans('general_quizzes.List Question'),
            'method' => 'GET',
            'key' => APIActionsEnums::List_HOMEWORK_QUESTION
        ];

        if (is_null($homework->published_at)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.homework.educational-supervisor.put.edit',
                    ['homework' => $homework->id]
                ),
                'label' => trans('general_quizzes.edit Homework'),
                'method' => 'GET',
                'key' => APIActionsEnums::EDIT_HOMEWORK
            ];
        }
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.educational-supervisor.delete.delete',
                ['homework' => $homework->id]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_HOMEWORK
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
        if ($homework->studentsAnswered()->count()) {
            $students = $homework->studentsAnswered;
            return $this->collection($students, new HwStudentTransformer($homework), ResourceTypesEnums::STUDENT);
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

    public function includeSubject(GeneralQuiz $periodicTest)
    {
        $subject = $periodicTest->subject;
        return $this->item(
            $subject,
            new \App\OurEdu\GeneralQuizzes\Lookup\Transformers\SubjectLookUpTransformer(),
            ResourceTypesEnums::SUBJECT
        );
    }

    public function includeGradeClass(GeneralQuiz $homework)
    {
        return $this->item($homework->gradeClass, new GradeClassLookUpTransformer(), ResourceTypesEnums::GRADE_CLASS);
    }
}
