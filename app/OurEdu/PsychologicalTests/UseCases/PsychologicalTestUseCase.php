<?php

namespace App\OurEdu\PsychologicalTests\UseCases;

use App\Exceptions\ErrorResponseException;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalTestRepositoryInterface;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalQuestionRepositoryInterface;

class PsychologicalTestUseCase implements PsychologicalTestUseCaseInterface
{
    protected $testRepository;
    protected $questionReportsitory;

    public function __construct(PsychologicalTestRepositoryInterface $testRepository, PsychologicalQuestionRepositoryInterface $questionReportsitory)
    {
        $this->testRepository = $testRepository;
        $this->questionReportsitory = $questionReportsitory;
    }


    public function startTest($user, $test)
    {
        $this->testRepository->deleteOldAnswers($user, $test);

        if (! $test->questions()->active()->count()) {
            throw new ErrorResponseException(trans('api.This test doesnt have any active questions'));
        }

        if ($test->options()->active()->count() < 2) {
            throw new ErrorResponseException(trans('api.Test is not ready yet'));
        }

        $question = $this->questionReportsitory->getActiveQuestions($user, $test);

        $message = $question->total() ? trans('api.Test started successfully') : trans('api.No more questions to answer!');

        return [
            'message'   =>  $message,
            'question'  =>  $question,
        ];
    }

    public function getNextQuestion($user, $test)
    {
        return $this->questionReportsitory->getActiveQuestions($user, $test);
    }

    public function answerQuestion($user, $test, $data)
    {
        $alreadyAnswered =  $test->answers()->where([
            'user_id'   =>  $user->id,
            'psychological_question_id'   =>  $data->question_id,
        ])->exists();

        if ($alreadyAnswered) {
            throw new ErrorResponseException(trans('api.You already answered this question'));
        }

        $answer = $this->questionReportsitory->answerTestQuestion($user, $test, $data);

        $question = $this->questionReportsitory->getActiveQuestions($user, $test);

        $message = trans('api.Answered successfully');

        return [
            'message'   =>  $message,
            'question'  =>  $question,
        ];
    }

    public function finishTest($user, $test)
    {
        if ($test->questions()->active()->count() > $test->answers()->where('user_id', $user->id)->count()) {
            throw new ErrorResponseException(trans('api.Test has more questions to answer'));
        }

        if (! $test->questions()->active()->count()) {
            throw new ErrorResponseException(trans('api.Test is not ready yet'));
        }

        $result = $this->calculateTestPercentage($test, $user);

        $message = trans('api.Finished successfully');

        return [
            'message'   =>  $message,
            'result'  =>  $result,
        ];
    }

    protected function calculateTestPercentage($test, $user)
    {
        // load required relations
        $test->load(['questions', 'options', 'answers' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->with('option');
        }]);

        // get active questions count
        $activeQuestionsCount = $test->questions->where('is_active', true)->count();

        // get the max option points
        $maxOptionPoints = $test->options->where('is_active', true)->max('points');

        // test total score
        $testTotalScore = $activeQuestionsCount * $maxOptionPoints;

        // calculate user points
        $userScore = $test->answers->pluck('option.points')->sum();

        // calculate percentage
        $percentage = ($userScore / $testTotalScore) * 100;

        // find the matching recomendation
        $recomendation = $test->recomendations()->where('from', '<=', $percentage)->where('to', '>=', $percentage)->first();

        // store the result
        return $test->results()->create([
            'user_id'   =>  $user->id,
            'psychological_recomendation_id' => $recomendation ? $recomendation->id : null,
            'percentage' => number_format($percentage, 2, '.', ''),
        ]);
    }
}
