<?php

namespace App\OurEdu\Quizzes\UseCases\StartQuizUseCase;

use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class StartQuizUseCase implements StartQuizUseCaseInterface
{
    private $quizRepository;
    private $student;

    public function __construct(
        QuizRepositoryInterface $quizRepository
    ) {
        $this->quizRepository = $quizRepository;
    }

    /**
     * @param $quizId
     * @return array
     */
    public function startQuiz($quizId): array
    {
        $this->student = Auth::guard('api')->user()->student;

        $quiz = $this->quizRepository->findOrFail($quizId);
        $quizRepo = $this->quizRepository->setQuiz($quiz);
        $quiz = $quizRepo->getQuiz();

        // further validations
        $validationErrors = $this->quizValidations($quiz);

        if ($validationErrors) {
            return $validationErrors;
        }

        // student has been start the quiz already
        $takenQuiz = $this->student->quizzes()->where('quiz_id', $quiz->id)->first();
        if ($takenQuiz) {
            if ($takenQuiz->status == QuizStatusEnum::FINISHED){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('api.this quiz has been already taken');
                $useCase['title'] = 'this quiz has been already taken';
                return $useCase;
            }
        }
        // creating the random order of quiz questions for this student
        $studentQuestionsOrderArray = $quiz->questions()->inRandomOrder()->pluck('id')->toArray();
        $questionsOrderString = implode(',', $studentQuestionsOrderArray);
        if (!$takenQuiz){
            StudentQuiz::create([
                'quiz_id' => $quiz->id,
                'questions_ids' => $questionsOrderString,
                'student_id' => $this->student->id,
                'quiz_type' => $quiz->quiz_type,
                'status' => QuizStatusEnum::STARTED,
                'started_at' => now(),
            ]);
        }
        $return['status'] = 200;
        $return['message'] = trans('quiz.The Quiz started successfully');
        $return['questions'] = $quizRepo->returnQuestion(1, $questionsOrderString);
        return $return;
    }

    private function quizValidations($quiz)
    {
        $this->student = Auth::guard('api')->user()->student;

        // quiz has not published yet
        if (is_null($quiz->published_at)) {
            $return['status'] = 422;
            $return['detail'] = trans('quiz.cant get un published quiz');
            $return['title'] = 'cant get un published quiz';
            return $return;
        }

        // quiz time passed
        if (!is_null($quiz->end_at)) {
            if (now() > $quiz->end_at) {
                $return['status'] = 422;
                $return['detail'] = trans('quiz.quiz time passed');
                $return['title'] = 'quiz time passed';
                return $return;
            }
        }

        // quiz time has not come yet
        if (!is_null($quiz->start_at)) {
            if (now() < $quiz->start_at) {
                $return['status'] = 422;
                $return['detail'] = trans('quiz.quiz time has not come yet');
                $return['title'] = 'quiz time has not come yet';
                return $return;
            }
        }

    }
}
