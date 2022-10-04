<?php

namespace App\OurEdu\Courses\Student\Controllers;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Repository\LiveSessionRepositoryInterface;
use App\OurEdu\Courses\Student\Middleware\Api\AvailableLiveSessionMiddleware;
use App\OurEdu\Courses\Transformers\LiveSessionListTransformer;
use App\OurEdu\Courses\Transformers\LiveSessionTransformer;
use App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\V2\CourseSubscribeUseCaseInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class LiveSessionApiController extends BaseApiController
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var LiveSessionRepositoryInterface
     */
    private $liveSessionRepository;

    private $user;

    private $params;
    /**
     * @var CourseSubscribeUseCaseInterface
     */
    private $courseSubscribeUseCase;

    public function __construct(
        LiveSessionRepositoryInterface $liveSessionRepository,
        UserRepositoryInterface $userRepository,
        CourseSubscribeUseCaseInterface $courseSubscribeUseCase
    ) {
        $this->liveSessionRepository = $liveSessionRepository;
        $this->userRepository = $userRepository;
        $this->courseSubscribeUseCase = $courseSubscribeUseCase;

        $this->user = Auth::guard('api')->user();
        $this->params = [];
        $this->middleware(AvailableLiveSessionMiddleware::class)->only('subscribe');
    }


    public function listAvailable()
    {
        $student = $this->user->student;
        $liveSessions = $this->liveSessionRepository->getRelatedLiveSessionsForStudent($student);

        return $this->transformDataModInclude($liveSessions, ['instructor', 'sessions', 'subject', 'actions'],
            new LiveSessionListTransformer($this->user), ResourceTypesEnums::LIVE_SESSION);
    }


    public function show($id)
    {
        $liveSession = $this->liveSessionRepository->findOrFail($id);

        $user = request('user_id') ? $this->userRepository->findOrFail(request('user_id')) : $this->user;
        if (request('auto_join')) {
            $this->params['auto_join'] = true;
        }
        $liveSession->load('sessions', 'instructor', 'subject');

        return $this->transformDataModInclude($liveSession, ['instructor', 'sessions', 'subject', 'actions'],
            new LiveSessionTransformer($user, $this->params), ResourceTypesEnums::LIVE_SESSION);
    }


    public function subscribe($liveSessionId)
    {
        try {
            $studentId = auth()->user()->student->id;
            $subscribe = $this->courseSubscribeUseCase->subscribeCourse($liveSessionId, $studentId, true);
            if ($subscribe['status'] == 200) {
                return response()->json([
                    'meta' => [
                        'message' => trans('app.Subscribed Successfully')
                    ]
                ]);
            } else {
                return formatErrorValidation($subscribe);
            }
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    public function subscribeAndJoin($liveSessionId)
    {
        try {
            $studentId = auth()->user()->student->id;
            $subscribeAndJoin = $this->courseSubscribeUseCase->subscribeCourse($liveSessionId, $studentId, true, true);
            if ($subscribeAndJoin['status'] == 200) {
                $data = [
                    'data' => [
                        'attributes' => [
                            'join_url' => $subscribeAndJoin['vcrSessionUrl']
                        ]
                    ]
                ];
                return \response()->json($data);
            } else {
                return formatErrorValidation($subscribeAndJoin);
            }
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }
}
