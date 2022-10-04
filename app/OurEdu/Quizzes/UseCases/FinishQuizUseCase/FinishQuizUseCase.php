<?php


namespace App\OurEdu\Quizzes\UseCases\FinishQuizUseCase;


use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Models\QuizQuestionAnswer;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class FinishQuizUseCase implements FinishQuizUseCaseInterface
{
    private $quizRepository;
    private $student;

    public function __construct(
        QuizRepositoryInterface $quizRepository
    ) {
        $this->quizRepository = $quizRepository;
    }

    public function finishQuiz(int $quizId): array
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
        if ($quizStudent = $this->student->quizzes()->where('quiz_id', $quiz->id)->first()) {
            if ($quizStudent->status == QuizStatusEnum::FINISHED) {
                $return['status'] = 422;
                $return['detail'] = trans('api.quiz has been finished already');
                $return['title'] = 'quiz has been finished already';
                return $return;
            }

            $quizFinalGrade = $quiz->questions()->pluck('question_grade')->sum();
            $studentRightAnswersArray = $quiz->answers()->where('student_id', $this->student->id)
                ->where('is_correct_answer', 1)->get()
                ->groupBy('question_id')->toArray();

            $studentAnswerResult = $quiz->questions()
                ->whereIn('id', array_keys($studentRightAnswersArray))
                ->pluck('question_grade')->sum();

            $percentage = $studentAnswerResult ? ($studentAnswerResult / $quizFinalGrade) * 100 : 0;

            $data=[
                'finished_at' => now(),
                'status' => QuizStatusEnum::FINISHED,
                'quiz_result_percentage'    =>  number_format($percentage, 2, '.', '')
            ];

            $quizStudent->update($data);
            $quiz->allStudentQuiz()
                ->where('student_id', $this->student->id)
                ->update([
                    'taken_at' => now(),
                    'quiz_result_percentage' =>  number_format($percentage, 2, '.', '')
                ]);

            $return['status'] = 200;
            $return['quiz'] = $quiz;
            $return['result'] = number_format($percentage, 2, '.', '');
            return $return;
        } else {
            $return['status'] = 422;
            $return['detail'] = trans('api.quiz has not started yet');
            $return['title'] = 'quiz has not started yet';
            return $return;
        }
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
