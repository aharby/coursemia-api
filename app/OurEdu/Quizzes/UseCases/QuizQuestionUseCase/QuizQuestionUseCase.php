<?php

namespace App\OurEdu\Quizzes\UseCases\QuizQuestionUseCase;

use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepository;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuizQuestionUseCase implements QuizQuestionUseCaseInterface
{
    private $quizRepository;
    private $user;

    public function __construct(
        QuizRepositoryInterface $quizRepository
    )
    {
        $this->quizRepository = $quizRepository;
        $this->user = Auth::guard('api')->user();
    }

    public function createOrUpdateQuizQuestions($quiz, $data)
    {
        $validationErrors = $this->questionsValidations($quiz, $data);

        if ($validationErrors) {
            return $validationErrors;
        }

        $quizRepo = new QuizRepository($quiz);

        // deleting the difference between the old questions and the new questions
        $this->deleteQuestions($quizRepo, $data->questions);

        $this->createOrUpdateQuestions($quizRepo, $quiz->id, $data->questions);
        $useCase['status'] = 200;
        return $useCase;
    }

    private function createOrUpdateQuestions($quizRepo, $quizId, $questions)
    {
        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionData = [
                'quiz_id' => $quizId,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'time_to_solve' => $question->time_to_solve ??  ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
                'question_grade' => $question->question_grade,
            ];
            if (Str::contains($questionId, 'new')) {
                $questionObj = $quizRepo->createQuestion($questionData);
            } else {
                $questionObj = $quizRepo->updateQuestion($questionId, $questionData);
            }
            $this->createOrUpdateOptions($quizRepo, $questionObj, $question->options);
        }
    }

    private function createOrUpdateOptions($quizRepo, $question, $options)
    {
        $this->deleteOptions($quizRepo, $question->id, $options);

        foreach ($options as $option) {
            $optionId = $option->id;

            $optionData = [
                'option' => $option->option,
                'is_correct_answer' => $option->is_correct_answer,
            ];
            if (Str::contains($optionId, 'new')) {
                $quizRepo->createOption($question, $optionData);
            } else {
                $quizRepo->updateOption($optionId , $question, $optionData);
            }
        }
    }

    // deleting the difference between the old questions and the new questions
    private function deleteQuestions(QuizRepository $quizRepo, $questions)
    {
        $newIds = Arr::pluck($questions, 'id');
        $oldQuestionsIds = $quizRepo->getQuestionsIds();
        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $quizRepo->deleteDeletedQuestionsOptions($deleteIds);
        $quizRepo->deleteQuestionsIds($deleteIds);
    }

    // deleting the difference between the old options and the new options
    private function deleteOptions(QuizRepository $quizRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $quizRepo->getQuestionOptionsIds($questionId);
        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $quizRepo->deleteOptions($questionId, $deleteIds);
    }

    private function questionsValidations($quiz, $data)
    {
        if (!is_null($quiz->published_at)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('api.cant edit published quiz');
            $useCase['title'] = 'cant edit published quiz';
            return $useCase;
        }

        if ($quiz->quiz_type == QuizTypesEnum::QUIZ) {
            if (collect($data->questions)->sum('time_to_solve') > QuizTimesEnum::QUIZ_TOTAL_TIME_IN_SECONDS) {
                $timeInMinutes =  QuizTimesEnum::QUIZ_TOTAL_TIME_IN_SECONDS/60;
                $useCase['status'] = 422;
                $useCase['detail'] = trans('quiz.quiz time must not exceed minutes',[
                    'minutes' => $timeInMinutes
                ]);
                $useCase['title'] = 'quiz exceeded the time limit';
                return $useCase;
            }
        }
    }

}
