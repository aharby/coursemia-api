<?php


namespace App\OurEdu\Quizzes\Parent\Controller\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Parent\QuizzesPerformance;
use App\OurEdu\Quizzes\Parent\Transformers\StudentQuizTransformer;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;

use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

use App\OurEdu\Quizzes\Parent\Transformers\QuizzesPerformanceTransformer;


class QuizApiController  extends BaseApiController
{
    private $allQuizzesModel;
    private $quizzesPerformance;
    private $quizRepository;
    protected $filters;

    public function __construct(
        AllQuizStudent $allQuizStudent,
        QuizRepositoryInterface $quizRepository,
        QuizzesPerformance $quizzesPerformance
    ) {
        $this->allQuizzesModel = $allQuizStudent;
        $this->quizRepository = $quizRepository;
        $this->quizzesPerformance = $quizzesPerformance;
        $this->setFilters();

//        $this->middleware('type:parent')
//            ->only(['listStudentQuizzes', 'getStudentQuizzesPerformance']);
    }

    public function getStudentQuizzesPerformance($studentId)
    {
        try {
            $user = User::query()
                ->whereHas("student")
                ->findOrFail($studentId);

            $studentQuizzes = $this->quizRepository->getStudentQuizzesByParent($user->student->id);

            $this->quizzesPerformance->completed_homework_percentage = $this->calcPercentageOfCompletedHomework($studentQuizzes);
            $this->quizzesPerformance->student_id = $studentId;

            return $this->transformDataModInclude(
                $this->quizzesPerformance, '',
                new QuizzesPerformanceTransformer(), ResourceTypesEnums::QUIZZES_PERFORMANCE
            );

        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    private function calcPercentageOfCompletedHomework($studentQuizzes)
    {
        $homeworkCount = $studentQuizzes->where('quiz_type', QuizTypesEnum::HOMEWORK)->count();

        if (!$homeworkCount) {
            return 0;
        }

        return $studentQuizzes
                ->where('quiz_type', QuizTypesEnum::HOMEWORK)
                ->pluck('quiz_result_percentage')
                ->sum() / $homeworkCount;
    }

    public function listStudentQuizzes($studentId)
    {
        $user = User::query()
            ->whereHas("student")
            ->findOrFail($studentId);

        $data = [
            'subject_id' => request()->subject_id,
            'quiz_type' => request()->quiz_type,
        ];
        $studentQuizzes = $this->quizRepository->getStudentQuizzesByParent($user->student->id, $data);

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude(
            $studentQuizzes, '',
            new StudentQuizTransformer(), ResourceTypesEnums::QUIZ, $meta
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
            'data' => QuizTypesEnum::getAllQuizTypes(),
            'trans' => false,
            'value' => request()->get('quiz_type'),
        ];
    }

}
