<?php


namespace App\OurEdu\GeneralQuizzes\EducationalSupervisor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HomeworkAllowedStudentsTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HwStudentTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class GeneralQuizReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
        "students",
        "Instructor",
        "Classrooms",
        'branch',
        'actions',

    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralQuiz $generalQuiz)
    {
        $transformedData = [
            'id' => (int)$generalQuiz->id,
            'title' => (string)$generalQuiz->title,
            'status' => (int)$generalQuiz->is_active,
            'random_question' => (bool)$generalQuiz->random_question,
            'quiz_type' => (string)$generalQuiz->quiz_type,
            'start_at' => (string)$generalQuiz->start_at,
            'end_at' => (string)$generalQuiz->end_at,
            'is_published' => (bool)!is_null($generalQuiz->published_at),
            'published_at' => (string)$generalQuiz->published_at,
            'branch_id' => (int)$generalQuiz->branch_id,
            'subject_id' => (int) $generalQuiz->subject_id,
            'grade_class_id' => (int) $generalQuiz->grade_class_id,
            'grade_class'=>(string)$generalQuiz->gradeClass->title,
            'subject'=>(string)$generalQuiz->subject->name,
            'school_account_id' => (int)$generalQuiz->school_account_id,
            'avg'=> (float)$this->getHomeWorkAvg($generalQuiz),
            'mark'=> (float)$generalQuiz->mark,
        ];
        if(isset($this->params['listScore']) && isset($this->params['students'])){
            $students = $this->params['students'];
            $transformedData['pagination'] = (object)[
                'per_page'=>$students->perPage(),
                'total'=>$students->total(),
                'current_page'=>$students->currentPage(),
                'count'=>$students->count(),
                'total_pages'=>$students->lastPage(),
                'next_page'=>$students->nextPageUrl(),
                'previous_page'=>$students->previousPageUrl()
            ];
        }
        return $transformedData;
    }

    private function getHomeWorkAvg(GeneralQuiz $homework)
    {
        $studentsGradsQry= $homework->studentsAnswered->where('is_finished','=',1);
        $studentsGradsAvg=0;
        if($studentsGradsQry->count()>0){
            $studentsGradsAvg=$studentsGradsQry->sum('score_percentage')/$studentsGradsQry->count();
        }
        return $studentsGradsAvg;
    }


    public function includeStudents($homework)
    {
        $studentsAnswered = $homework->studentsAnswered()->with('user.student');
        $students = $this->params['students'];
        $studentsAnswered = $studentsAnswered->pluck('score','student_id')->toArray();

        return $this->collection($students, new GeneralQuizStudentTransformer($homework,$studentsAnswered), ResourceTypesEnums::STUDENT);
    }

    public function includeActions(GeneralQuiz $generalQuiz)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.general-quizzes.educational-supervisor-reports.get.listStudentsScores', ['generalQuiz' => $generalQuiz->id]),
            'label' => trans('app.Export by questions grades'),
            'method' => 'GET',
            'key' => APIActionsEnums::EXPORT_BY_QUESTIONS_GRADES
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeInstructor($homework)
    {
        return $this->item($homework->creator, new UserTransformer(), ResourceTypesEnums::INSTRUCTOR);
    }
    public function includeClassrooms($homework)
    {
        return $this->collection($homework->classrooms, new ClassroomTransformer(), ResourceTypesEnums::CLASSROOM);
    }
    public function includeBranch($homework)
    {
        return $this->item($homework->branch, new BranchTransformer(), ResourceTypesEnums::SCHOOL_BRANCHES);
    }

}
