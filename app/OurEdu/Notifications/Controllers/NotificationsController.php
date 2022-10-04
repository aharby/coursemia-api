<?php

namespace App\OurEdu\Notifications\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Repository\NotificationRepositoryInterface;
use stdClass;

class NotificationsController extends BaseApiController
{
    private $repository;
    private $notifierFactory;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository, NotifierFactoryInterface $notifierFactory
    )
    {
        $this->repository = $notificationRepository;
        $this->notifierFactory = $notifierFactory;
    }

    public function getNotifications()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate();
        $holderObject = new stdClass();
        $holderObject->notifications_count = $user->notifications()->count();

        $holderObject->notificationsData = array_map(function ($notification) {
            $notification->title = trans($notification->data['title']);
            $notification->body = trans($notification->data['body']);
            return $notification;
        },  $notifications->items());
        return response()->json([
            'notifications' => $holderObject
        ]);
    }

}
