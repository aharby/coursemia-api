<?php

namespace App\OurEdu\Notifications\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseNotification\MailNotification\NotificationMailTemplate\NotificationMailTemplate;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Repository\NotificationRepositoryInterface;
use App\OurEdu\Notifications\Transformers\ListNotificationsTransformer;
use App\OurEdu\Notifications\Transformers\NotificationTransformer;
use App\OurEdu\Users\Auth\Requests\api\sendFcmNotificationRequest;
use App\OurEdu\Users\Models\FirebaseToken;
use App\OurEdu\Users\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class NotificationApiController extends BaseApiController
{
    private $parserInterface;
    private $repository;
    private $notifierFactory;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        NotifierFactoryInterface $notifierFactory,
        ParserInterface $parserInterface
    )
    {
        $this->repository = $notificationRepository;
        $this->user = Auth::guard('api')->user();
        $this->notifierFactory = $notifierFactory;
        $this->parserInterface = $parserInterface;
    }

    public function getAllNotifications()
    {
        $page = request()->page ?? 1;
        $notifications = $this->user->notifications()->jsonPaginate(8, ['*'], 'notifications-page', $page);
        $holderObject = new \stdClass();
        $holderObject->notifications_count = $this->user->notifications()->count();
        $holderObject->notificationsData = $notifications;
        $meta[] = ['pagination' => [
            'per_page' => $notifications->perPage(),
            'total' => $notifications->total(),
            'current_page' => $notifications->currentPage(),
            'count' => $notifications->count(),
            'total_pages' => $notifications->lastPage(),
            'next_page' => $notifications->nextPageUrl(),
            'previous_page' => $notifications->previousPageUrl()
        ]];

        return $this->transformDataModInclude($holderObject, '', new ListNotificationsTransformer(),
            ResourceTypesEnums::NOTIFICATION,$meta);
    }

    public function getUnreadNotificationsCount()
    {
        $count = $this->user->unreadNotifications()->count();
        $data = [
            'data' => [
                'attributes' => [
                    'unread_count' => $count
                ]
            ]
        ];
        return \response()->json($data);
    }

    public function markNotificationAsRead($id)
    {
        $notification = $this->user->notifications()->findOrFail($id);
        if (!is_null($notification->read_at)) {
            $meta = [
                'message' => trans('notification.notification already read')
            ];
            return response()->json(['meta' => $meta], 200);
        }
        $notification->markAsRead();
        return $this->transformDataModInclude($notification, '', new NotificationTransformer(),
            ResourceTypesEnums::NOTIFICATION);
    }

    public function sendNotification(sendFcmNotificationRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData()->toArray();

        // if send user id or email find user and send him fcm notification
        if (isset($data['user_id']))
            $user = User::find($data['user_id']);

        if (isset($data['email']))
            $user = User::where('email', $data['email'])->first();

        // if send device_token
        if (isset($data['device_token'])) {
            // find token user and send him notification
            $firebaseToken = FirebaseToken::where('device_token', $data['device_token'])->first();
            if ($firebaseToken) {
                $user = User::find($firebaseToken->user_id);
            } else {
                // no user related to the token then assign it to current token user
                $user = $this->user ?? User::find(1); // if not login assign token to any dummy user
                $firebaseData['device_token'] = $data['device_token'];
                if (isset($data['device_type'])) {
                    $firebaseData['device_type'] = $data['device_type'];
                }

                $user->firebaseTokens()->create($firebaseData);
            }
        }
        $params = ['param1' => 'value1', 'param2' => 'value2'];

        if (isset($data['params'])) {
            $params = $data['params'];
        }
        $url = 'https://google.com';
        if (isset($data['click_action'])) {
            $url = $data['click_action'];
        }
        $notificationData = [
            'users' => collect([$user]),
            'fcm' => [
                'data' => [
                    'title' => 'dummy ',
                    'body' => 'dummy  body',
                    'data' => $params,
                    'url' => $url
                ]
            ]
        ];

        $this->notifierFactory->send($notificationData);
    }

    public function updateUserToken(sendFcmNotificationRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData()->toArray();

        $user = $this->user;
        $fireBaseData = array();
        $fireBaseData['fingerprint'] = '';
        if (isset($data['device_token'])) {
            $fireBaseData['device_token'] = $data['device_token'];
        }
        if (isset($data['fingerprint'])) {
            $fireBaseData['fingerprint'] = $data['fingerprint'];
        }
        if (isset($data['device_type'])) {
            $fireBaseData['device_type'] = $data['device_type'];
        }

        $fireBaseToken = $user->firebaseTokens()->where('device_type', $data['device_type'])
            ->where('fingerprint', $fireBaseData['fingerprint'])
            ->first();

        if (!$fireBaseToken) {
            $user->firebaseTokens()->create($fireBaseData);
        } else {
            $fireBaseToken->update($fireBaseData);
        }

        return response()->json([
            'meta' => [
                'message' => trans('app.Update successfully')
            ]
        ]);
    }

    public function senTestEmail(\Illuminate\Http\Request $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData()->toArray();
        $email= $data['email'];
        Mail::to([$email])
            ->send(new NotificationMailTemplate('student',
                'activateAccountEmail',
                ['url' => '','lang'=>'en'],
                'test'));

    }
}
