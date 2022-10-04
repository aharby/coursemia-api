<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\UseCases;


use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\UpdatePeriodicTestUseCase\UpdatePeriodicTestUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use Carbon\Carbon;
use Swis\JsonApi\Client\Interfaces\ItemInterface;

class RetakeGeneralQuizUseCase implements RetakeGeneralQuizUseCaseInterface
{
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;
    /**
     * @var UpdatePeriodicTestUseCaseInterface
     */
    private $updatePeriodicTestUseCase;

    /**
     * RetakeGeneralQuizUseCase constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param UpdatePeriodicTestUseCaseInterface $updatePeriodicTestUseCase
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository, UpdatePeriodicTestUseCaseInterface $updatePeriodicTestUseCase)
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->updatePeriodicTestUseCase = $updatePeriodicTestUseCase;
    }

    public function retake(GeneralQuiz $generalQuiz, ItemInterface $data)
    {
        $validation = $this->validateReplicateGeneralQuiz($generalQuiz, $data);

        if ($validation) {
            return $validation;
        }

        $retakenQuiz = $generalQuiz->replicate();

        $retakenQuiz->title = "[retake] " . $generalQuiz->title;
        $retakenQuiz->start_at  = $data->start_at;
        $retakenQuiz->end_at  = $data->end_at;
        $retakenQuiz->published_at = null;
        $retakenQuiz->save();

        $this->replicateGeneralQuizQuestion($generalQuiz, $retakenQuiz);

        if(isset($data->students)){
            $this->generalQuizRepository->saveGeneralQuizStudents($retakenQuiz, $data->students->pluck('id')->toArray());
        }

        $this->updatePeriodicTestUseCase->publishPeriodicTest($retakenQuiz);

        $useCase['periodicTest'] = $retakenQuiz->refresh();
        $useCase['meta'] = [
            'message' => trans('general_quizzes.periodicTest_created')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    private function replicateGeneralQuizQuestion(GeneralQuiz $fromQuiz, GeneralQuiz $toQuiz)
    {
        $quizQuestions = $fromQuiz->questions()->pluck("id")->toArray();

        $toQuiz->questions()->sync($quizQuestions);
    }

    private function validateReplicateGeneralQuiz(GeneralQuiz $generalQuiz, ItemInterface $data)
    {
        /**
         * check if test time is valid
         */
        $test_time_limit = Carbon::parse($data['start_at'])
            ->diffInSeconds(Carbon::parse($data['end_at']));

        if($generalQuiz->test_time > $test_time_limit){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.test time must be less than test time limits');
            $useCase['title'] = 'Test time exceeds limits';
            return $useCase;
        }

        /**
         *  check if the origin quiz is disabled
         */
        if (!$generalQuiz->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans("general_quizzes.you can't reassign the disable quiz", ['type' => trans('general_quizzes.'.$generalQuiz->quiz_type)]);
            $useCase['title'] = 'has been assigned before';
            return $useCase;
        }

        /**
         *  check if the origin quiz is finished
         */
        if ($generalQuiz->end_at >= Carbon::now()) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.the quiz end time must be passed to give a new chance to retake');
            $useCase['title'] = 'quiz not finished yet';
            return $useCase;
        }



        /**
         *  check that the students that given the chance to retake the exam were
         * already from students that  had the right to access the original quiz
         */
        $generalQuizStudent = $generalQuiz->students()->pluck("id")->toArray();
        $newQuizStudent = $data->students->pluck('id')->toArray();

        if (!$generalQuizStudent) {
            $quizClassrooms = $generalQuiz->classrooms()->pluck("id")->toArray();

            $generalQuizStudent  = Student::query()
                ->whereIn("classroom_id", $quizClassrooms)
                ->pluck("user_id")
                ->toArray();
        }

        if (count($generalQuizStudent) and count(array_intersect($generalQuizStudent, $newQuizStudent)) != count($newQuizStudent)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.the quiz students must be students from the old students');
            $useCase['title'] = 'Students not existed in origin quiz users';
            return $useCase;
        }

        return null;
    }
}

