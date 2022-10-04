<?php

namespace App\OurEdu\Subscribes\Transformers;

use App\OurEdu\Packages\Package;
use App\OurEdu\SubjectPackages\Parent\Transformers\PackageTransformer;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subscribes\Subscription;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Courses\Transformers\CourseTransformer;
use App\OurEdu\Payments\Transformers\OrderTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\SubjectTransformer;

class SubscriptionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'user',
        'subscription_subject',
        'creator',
        'order',
        'actions',
    ];

    private $student;

    public function __construct($student = null)
    {
        $this->student = $student;
    }

    public function transform(Subscription $subscription)
    {
        $transformerDatat = [
            'id' => (int) $subscription->id,
            'user_id' => (int) $subscription->user_id,
            'payment_done' => (boolean) $subscription->payment_done,
        ];

        return $transformerDatat;
    }

    public function includeUser(Subscription $subscription)
    {
        if ($subscription->user) {
            return $this->item($subscription->user, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeCreator(Subscription $subscription)
    {
        if ($subscription->creator()->exists()) {
            return $this->item($subscription->creator, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeOrder(Subscription $subscription)
    {
        if ($subscription->order) {
            return $this->item($subscription->order, new OrderTransformer(), ResourceTypesEnums::ORDER);
        }
    }

    public function includeSubscriptionSubject(Subscription $subscription)
    {
        if ($subscription->subscripable && $subscription->subscripable instanceof Course) {
            return $this->item($subscription->subscripable, new CourseTransformer(), ResourceTypesEnums::COURSE);
        }

        if ($subscription->subscripable && $subscription->subscripable instanceof Subject) {
            return $this->item($subscription->subscripable, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }

        if ($subscription->subscripable && $subscription->subscripable instanceof Package) {
            return $this->item($subscription->subscripable, new PackageTransformer(), ResourceTypesEnums::SUBJECT_PACKAGE);
        }
    }

    public function includeActions(Subscription $subscription)
    {
        $actions = [];


        if (Auth::guard('api')->user()->type == UserEnums::PARENT_TYPE) {
            if ($this->student && ! $subscription->payment_done) {

            // case logged user is the sender
                if ($this->student->wallet_amount <= $subscription->subscripable->subscription_cost) {
                    // add money to wallet action action
                    $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.parent.payments.submitTransaction', ['student_id' => $this->student->id]),
                    'label' => trans('subscriptions.Add money'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::ADD_MONEY_TO_WALLET
                    ];
                } else {
                    $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.parent.subscriptions.subscriptionPayment', ['id' => $subscription->id]),
                    'label' => trans('subscriptions.Buy now'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::BUY_SUBSCRIPTION
                    ];
                }
            }
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
