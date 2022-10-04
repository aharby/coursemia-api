<?php

namespace App\OurEdu\Feedbacks\Student\Controllers\Api;

use App\Http\Controllers\Controller;
use App\OurEdu\Feedbacks\Repository\FeedbackRepositoryInterface;
use App\OurEdu\Feedbacks\Student\Requests\FeedbackRequest;
use Swis\JsonApi\Client\Interfaces\ParserInterface;


class FeedbacksApiController extends Controller
{
    private $repository;
    private $parserInterface;

    public function __construct(
        ParserInterface $parserInterface,
        FeedbackRepositoryInterface $feedbackRepository
    ) {
        $this->repository = $feedbackRepository;
        $this->parserInterface = $parserInterface;
    }

    public function postFeedback(FeedbackRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $studentId = auth()->user()->student->id;
        try {
            $feedback = $this->repository->create([
                'feedback' => $data->feedback,
                'student_id' => $studentId
            ]);
            if ($feedback){
                return response()->json(
                    [
                        'meta' => [
                            'message' => trans('feedbacks.Feedback sent Successfully')
                        ]
                    ]
                );
            }
        } catch (\Exception $e) {
            $errorArray = [
                'status' => $e->getCode(),
                'title' => $e->getMessage(),
                'detail' => $e->getTrace()
            ];
            return formatErrorValidation($errorArray, 500);
        }

    }

}
