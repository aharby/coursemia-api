<?php


namespace App\OurEdu\GeneralQuizzes\Parent\Controller\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizzesPerformance;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

use App\OurEdu\GeneralQuizzes\Parent\Transformers\GeneralQuizPerformanceTransformer;
use App\OurEdu\GeneralQuizzes\Parent\Transformers\StudentGeneralQuizAnswersTransformer;
use App\OurEdu\GeneralQuizzes\Parent\Transformers\StudentGeneralQuizTransformer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Parent\Transformers\PaginateStudentAnswer;
class GeneralQuizController  extends BaseApiController
{
    private $generalQuizRepo;
    private $generalQuizzesPerformance;
    protected $filters;
    private $generalQuizStudentRepository;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        GeneralQuizzesPerformance $generalQuizzesPerformance,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
    ) {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->generalQuizzesPerformance = $generalQuizzesPerformance;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;

       $this->middleware('type:parent');
    }

    public function listStudentGeneralQuiz($studentId)
    {
        $user = User::query()
            ->whereHas("student")
            ->findOrFail($studentId);

        $this->setFilters();

        $studentQuizzes = $this->generalQuizRepo->getStudentGeneralQuizzesByParent($user, $this->filters);
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        $params = ['student'=> $user];
        return $this->transformDataModInclude(
            $studentQuizzes, '',
            new StudentGeneralQuizTransformer($params), ResourceTypesEnums::GENERAL_QUIZ, $meta
        );
    }

    protected function setFilters()
    {
        $studentId = request()->route('studentId');
        $user = User::query()
            ->whereHas("student")
            ->findOrFail($studentId);

        $studentSubjects = $user->student
            ->subjects()
            ->get()
            ->pluck('name' , 'id')
            ->toArray();

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $studentSubjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];
        $this->filters[] = [
            'name' => 'quiz_type',
            'type' => 'select',
            'data' => GeneralQuizTypeEnum::getAllQuizTypes(),
            'trans' => false,
            'value' => request()->get('quiz_type'),
        ];
    }

    public function getStudentGeneralQuizAnswers(GeneralQuiz $generalQuiz,User $student)
    {
        $studentGeneralQuiz=$this->generalQuizStudentRepository->findStudentGeneralQuiz($generalQuiz->id,$student->id);
        return $this->transformDataModInclude($studentGeneralQuiz, 'questions', new StudentGeneralQuizAnswersTransformer($generalQuiz,$student), ResourceTypesEnums::STUDENT_QUIZ_ANSWERS);
    }

      /*
     * return pagination of student correct answer or not
     * */
    public function getStudentAnswersSolved(GeneralQuiz $generalQuiz,User $student)
    {
        return $this->transformDataModInclude(['data'=>'fale'],'',new PaginateStudentAnswer($generalQuiz,$student),ResourceTypesEnums::GENERAL_QUIZ);
    }


}
