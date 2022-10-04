<?php


namespace App\OurEdu\Quizzes\UseCases\AnswerQuizQuestionUseCase;


use App\OurEdu\Quizzes\Enums\QuizQuestionsTypesEnum;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class AnswerQuizQuestionUseCase implements AnswerQuizQuestionUseCaseInterface
{
    private $quizRepository;
    private $student;

    public function __construct(
        QuizRepositoryInterface $quizRepository
    ) {
        $this->quizRepository = $quizRepository;
    }

    public function postAnswer(int $quizId, Collection $data)
    {
        $this->student = Auth::guard('api')->user()->student;
        $quiz = $this->quizRepository->findOrFail($quizId);
        // further validations
        $validationErrors = $this->quizValidations($quiz);

        if ($validationErrors) {
            return $validationErrors;
        }

        if ($data && $data->isNotEmpty()) {
            // the whole question answer is correct
            $data->each(function ($question){
                $questionId = $question->getId();
                $quizQuestion = $this->quizRepository->findQuestionOrFail($questionId);

                // delete previous answer for this questions
                $this->quizRepository->deleteQuestionAnswers($quizQuestion, $this->student->id);

                switch ($quizQuestion->question_type) {
                    case QuizQuestionsTypesEnum::TRUE_FALSE:
                        $this->trueFalseQuestion($quizQuestion, $question->answers);
                        break;
                    case QuizQuestionsTypesEnum::MULTIPLE_CHOICE:
                        $this->multipleChoiceQuestion($quizQuestion, $question->answers);
                        break;
                }
            });
        }
        $return['status'] = 200;
        return $return;
    }

    private function trueFalseQuestion($quizQuestion, $answers)
    {
        $this->student = Auth::guard('api')->user()->student;
        $answer = $answers->first();
        $answerId = $answer->answer_id ?? null;
        $answer = $this->quizRepository->findOptionOrFail($answerId);

        // is_correct_option = is_correct_answer here because true_false is a single answer question
        $answerData = [
            'quiz_id' => $quizQuestion->quiz_id,
            'question_id' => $quizQuestion->id,
            'question_grade' => $quizQuestion->question_grade,
            'student_id' => $this->student->id,
            'is_correct_option' => $answer->is_correct_answer,
            'is_correct_answer' => $answer->is_correct_answer,
            'option_id' => $answerId,
        ];

        $this->quizRepository->insertAnswer($quizQuestion, $answerData);
    }

    private function multipleChoiceQuestion($quizQuestion, $answers)
    {
        $this->student = Auth::guard('api')->user()->student;
        $answersArray = [];
        // should be passed by reference
        $isCorrectAnswer = 0;
        $isCorrectQuestionArray = [];
        foreach ($answers as $answer) {
            $answerId = $answer->answer_id ?? null;
            $answer = $this->quizRepository->findOptionOrFail($answerId);

            // is_correct_answer != is_correct_option because the question is multiple choice
            $isCorrectQuestionArray[] = (bool) $answer->is_correct_answer;

            $answerData = [
                'quiz_id' => $quizQuestion->quiz_id,
                'question_id' => $quizQuestion->id,
                'question_grade' => $quizQuestion->question_grade,
                'student_id' => $this->student->id,
                'is_correct_option' => $answer->is_correct_answer,
                'is_correct_answer' => &$isCorrectAnswer,
                'option_id' => $answerId,
            ];
            $answersArray[] = $answerData;
        }
        $this->checkCorrectAnswer($isCorrectQuestionArray, $isCorrectAnswer);
        $this->quizRepository->insertManyAnswers($quizQuestion, $answersArray);
    }

    private function checkCorrectAnswer($isCorrectQuestionArray, &$isCorrectAnswer)
    {
        $this->student = Auth::guard('api')->user()->student;

        if (in_array(false, $isCorrectQuestionArray, true)) {
            $isCorrectAnswer = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectAnswer = true;
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
