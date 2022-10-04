<?php

namespace App\OurEdu\Subscribes\Parent\Controllers;

use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepositoryInterface;
use App\OurEdu\Subscribes\Parent\Middleware\ParentChildMiddleware;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subscribes\Parent\Requests\SubscriptionRequest;
use App\OurEdu\Subscribes\Transformers\SubscriptionTransformer;
use App\OurEdu\Subscribes\UseCases\SubscriptionUseCaseInterface;
use App\OurEdu\Subscribes\Repository\SubscriptionRepositoryInterface;

class SubscriptionsApiController extends BaseApiController
{
    protected $courseRepository;
    protected $subjectRepository;
    protected $subscriptionRepository;
    private $subjectPackageRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ParserInterface $parserInterface,
        SubscriptionUseCaseInterface $subscriptionUseCase,
        CourseRepositoryInterface $courseRepository,
        SubjectPackageRepositoryInterface $subjectPackageRepository,
        SubjectRepositoryInterface $subjectRepository,
        SubscriptionRepositoryInterface $subscriptionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->parserInterface = $parserInterface;
        $this->subscriptionUseCase = $subscriptionUseCase;
        $this->subjectPackageRepository = $subjectPackageRepository;
        $this->user = Auth::guard('api')->user();

        $this->middleware('type:parent');
        $this->courseRepository = $courseRepository;
        $this->subjectRepository = $subjectRepository;
        $this->subscriptionRepository = $subscriptionRepository;

        $this->middleware(ParentChildMiddleware::class)->except(['subscriptionPayment', 'userSubscriptions']); //subscriptionPayment has another way of validation
    }

    /**
     * Parent assign course for a student
     * @param  SubscriptionRequest $request
     * @param  int              $id
     * @return mixed
     */
    public function courseSubscripe(SubscriptionRequest $request, $id, $studentId)
    {


        $course = $this->courseRepository->findOrFail($id);

        $subscription = $this->subscriptionUseCase->subscripeCourse($studentId, $course);

        $meta = [
            "meta" => [
                "message" => trans('api.Subscription done')
            ]
        ];
        return response()->json($meta,200);
    }

    /**
     * Parent assign subject for a student
     * @param  SubscriptionRequest $request
     * @param  int              $id
     * @return mixed
     */
    public function subjectSubscripe(SubscriptionRequest $request, $id, $studentId)
    {

        $subject = $this->subjectRepository->findOrFail($id);

        $subscription = $this->subscriptionUseCase->subscripeSubject($studentId, $subject);

        $meta = [
            "meta" => [
                "message" => trans('api.Subscription done')
            ]
        ];
        return response()->json($meta,200);
    }

    public function subjectPackageSubscribe(SubscriptionRequest $request, $id,$studentId){


        $subjectPackage = $this->subjectPackageRepository->findOrFail($id);

        $subscription = $this->subscriptionUseCase->subscribeSubjectPackage($studentId, $subjectPackage);

        $meta = [
            "meta" => [
                "message" => trans('api.Subscription done')
            ]
        ];
        return response()->json($meta,200);
    }

    /**
     * Parent list student assignments
     * @param  int $userId
     * @return mixed
     */
    public function userSubscriptions($userId)
    {

        $student = $this->userRepository->findOrFail($userId)->student;

        $subscriptions = $this->subscriptionRepository->getUserSubscriptions($userId);

        $include = [
            'user.student',
            'actions',
            'subscription_subject',
            'order',
            'creator',
        ];

        return $this->transformDataModInclude($subscriptions, $include, new SubscriptionTransformer($student), ResourceTypesEnums::SUBSCRIPTION);
    }

    /**
     * Parent pays for a student assignment
     * @param  int $id
     * @return mixed
     */
    public function subscriptionPayment($id)
    {
        //validation logic
        $subscription = $this->subscriptionRepository->findOrFail($id);

        if (! $this->user->students()->with('user')->get()->where('user.id', $subscription->user_id)->first()) {
            throw new ErrorResponseException(trans('api.You are not related to this child'));
        }

        //business logic
        $subscription = $this->subscriptionUseCase->subscriptionPayment($id);

        $include = [
            'order',
            'actions',
            'creator',
            'user.student',
            'subscription_subject',
        ];

        $meta = [
            'message'   =>  trans('api.Thanks for pruchase')
        ];

        return $this->transformDataModInclude($subscription, $include, new SubscriptionTransformer(), ResourceTypesEnums::SUBSCRIPTION, $meta);
    }
}
