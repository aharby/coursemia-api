<?php

namespace App\OurEdu\PsychologicalTests\Student\Controllers;

use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\PsychologicalTests\Transformers\PsychologicalTestTransformer;
use App\OurEdu\PsychologicalTests\UseCases\PsychologicalTestUseCaseInterface;
use App\OurEdu\PsychologicalTests\Transformers\PsychologicalResultTransformer;
use App\OurEdu\PsychologicalTests\Transformers\PsychologicalQuestionTransformer;
use App\OurEdu\PsychologicalTests\Transformers\PsychologicalTestListTransformer;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalTestRepositoryInterface;
use App\OurEdu\PsychologicalTests\Student\Requests\Api\PsychologicalAnswerRequest;

class PsychologicalTestApiController extends BaseApiController
{
    protected $testUseCase;

    public function __construct(
        PsychologicalTestRepositoryInterface $testRepostitory,
        PsychologicalTestUseCaseInterface $testUseCase,
        ParserInterface $parserInterface
    ) {
        $this->repository = $testRepostitory;
        $this->parserInterface = $parserInterface;

        $this->user = Auth::guard('api')->user();
        $this->testUseCase = $testUseCase;
        $this->middleware('auth:api');
    }

    public function getIndex()
    {
        $tests = $this->repository->paginateWhere(['is_active' => true]);

        return $this->transformDataModInclude($tests, 'actions', new PsychologicalTestListTransformer($this->user), ResourceTypesEnums::PSYCHOLOGICAL_TEST);
    }

    public function getView($id)
    {
        $test = $this->repository->findOrFail($id);

        return $this->transformDataModInclude($test, ['actions', 'result.recomendation'], new PsychologicalTestTransformer($this->user), ResourceTypesEnums::PSYCHOLOGICAL_TEST);
    }

    public function getStart($id)
    {
        $test = $this->repository->findOrFail($id);

        $response = $this->testUseCase->startTest($this->user, $test);

        $page = request('page') ?? 1;
        $page++;

        $params = [
            'answer_endpoint' => true,
            'next_page' => $response['question']->hasMorePages() ? buildScopeRoute('api.student.psychological_tests.get.questions', ['id' => $id, 'page' => $page]) : null
        ];

        $meta = [
            'message' =>  $response['message']
        ];

        return $this->transformDataModInclude($response['question'], ['actions', 'test.activeOptions'], new PsychologicalQuestionTransformer($this->user, $params), ResourceTypesEnums::PSYCHOLOGICAL_QUESTION, $meta);
    }

    public function getNotAnsweredQuestions($id)
    {
        $test = $this->repository->findOrFail($id);

        $question = $this->testUseCase->getNextQuestion($this->user, $test);

        $params = [
            'answer_endpoint' => true,
            'next_page' => $question->hasMorePages() ? $question->nextPageUrl() : null

        ];

        return $this->transformDataModInclude($question, ['actions', 'test.activeOptions'], new PsychologicalQuestionTransformer($this->user, $params), ResourceTypesEnums::PSYCHOLOGICAL_QUESTION);
    }

    public function answerQuestion(PsychologicalAnswerRequest $request, $id)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $test = $this->repository->findOrFail($id);

        $response = $this->testUseCase->answerQuestion($this->user, $test, $data);

        $meta = [
            'message' =>  $response['message']
        ];

        if ($response['question']->hasMorePages()) {
            return response()->json([
                'meta'  =>  $meta
            ]);
        }

        $actions = [
            [
                'endpoint_url' => buildScopeRoute('api.student.psychological_tests.post.finish', ['id' => $id]),
                'label' => trans('app.Finish'),
                'method' => 'POST',
                'key' => APIActionsEnums::FINISH_PSYCHOLOGICAL_TEST
            ]
        ];

        return $this->transformDataModInclude($actions, '', new ActionTransformer(), ResourceTypesEnums::ACTION, $meta);
    }

    public function getFinish($id)
    {
        $test = $this->repository->findOrFail($id);

        $response = $this->testUseCase->finishTest($this->user, $test);

        $meta = [
            'message' =>  $response['message']
        ];

        return $this->transformDataModInclude($response['result'], ['recomendation', 'test'], new PsychologicalResultTransformer(), ResourceTypesEnums::PSYCHOLOGICAL_RESULT, $meta);
    }
}
