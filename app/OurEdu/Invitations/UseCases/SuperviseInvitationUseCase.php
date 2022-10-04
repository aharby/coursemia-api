<?php

namespace App\OurEdu\Invitations\UseCases;

use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactory;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Users\Models\StudentTeacherStudent;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Invitations\Repository\InvitationRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SuperviseInvitationUseCase implements SuperviseInvitationUseCaseInterface
{
    protected $user;
    protected $userRepository;
    protected $invitationRepository;
    protected $notifierFactory;

    public function __construct(
        UserRepositoryInterface $userRepository,
        InvitationRepositoryInterface $invitationRepository,
        NotifierFactory $notifierFactory
    )
    {
        $this->user = Auth::guard('api')->user();
        $this->userRepository = $userRepository;
        $this->invitationRepository = $invitationRepository;
        $this->notifierFactory = $notifierFactory;
    }

    /**
     * Remove parent or student relation
     * @param integer $id
     * @return void
     */
    public function removeRelation($id)
    {
        if ($this->user->type == UserEnums::PARENT_TYPE && $this->userRepository->find($id)->type == UserEnums::STUDENT_TYPE)
            $this->user->students()->detach($id);
        elseif ($this->user->type == UserEnums::STUDENT_TYPE && $this->userRepository->find($id)->type == UserEnums::PARENT_TYPE)
            $this->user->parents()->detach($id);
        elseif ($this->user->type == UserEnums::STUDENT_TYPE && $this->userRepository->find($id)->type == UserEnums::STUDENT_TEACHER_TYPE)
            $this->user->teachers()->detach($id);
        else
            $this->user->supervisedStudents()->detach($id);
    }


    public function checkIfSentBeforeWherePending($email)
    {
        return $this->invitationRepository->findByEmailAndSenderWhereStatusIn($email, $this->user->id, [InvitationEnums::PENDING]);
    }

    /**
     * Send supervise invitation
     * @param string $email
     * @return Invitation
     */
    public function superviseInvitation($email, $type, array $subjects = [], bool $abilitiesUser = false)
    {
        // already related student case
        if ($this->user->type == UserEnums::STUDENT_TYPE) {
            if ($this->user->parents()->where('email', $email)->exists()) {
                throw new ErrorResponseException(trans('api.You are in a relation with that email owner'));
            }

            if ($this->user->teachers()->where('email', $email)->exists()) {
                throw new ErrorResponseException(trans('api.You are in a relation with that email owner'));
            }
        }

        // already related parent case
        if ($this->user->type == UserEnums::PARENT_TYPE) {
            if ($this->user->students()->where('email', $email)->first()) {
                throw new ErrorResponseException(trans('api.You are in a relation with that email owner'));
            }
        }

        // already related teacher case
        if ($this->user->type == UserEnums::STUDENT_TEACHER_TYPE) {
            if ($this->user->supervisedStudents()->where('email', $email)->exists()) {
                throw new ErrorResponseException(trans('api.You are in a relation with that email owner'));
            }
        }

        if ($this->user->email == $email) {
            throw new ErrorResponseException(trans('api.You cannot invite yourself'));
        }

        if (Cache::has("{$this->user->id}_invited_{$email}")) {
            throw new ErrorResponseException(
                trans(
                    'api.You may send the person invitation once every :time minutes',
                    ["time" => env('INVITATION_CACHE_TIME', 2)]
                )
            );
        }
        $receiver = $this->userRepository->findByEmail($email, $abilitiesUser);
        // create invitation and notify receiver
        return $this->handleInvitationSending($email, $type, $subjects, $receiver);
    }


    /**
     * @param $invitationId
     * @return \App\OurEdu\Invitations\Models\Invitation|mixed|null
     * @throws ErrorResponseException
     */
    public function resendSuperviseInvitation($invitationId, $type,bool $abilitiesUser = false)
    {
        $invitation = $this->invitationRepository->findOrFail($invitationId);

        if (Cache::has("{$this->user->id}_invited_{$invitation->receiver_email}")) {
            throw new ErrorResponseException(trans('api.You may send the person invitation once every :time minutes', ["time" => env('INVITATION_CACHE_TIME', 2)]));
        }

        $invitation->touch();

        if ($user = $this->userRepository->findByEmail($invitation->receiver_email, $abilitiesUser)) {
            $this->sendSuperviseNotifications($user, $invitation);

            return $invitation;
        }
        $this->sendSuperviseMail($invitation->receiver_email, $invitation, $type);


        return $invitation;
    }

    /**
     * Accept | Refuse invitation
     * @param integer $id
     * @return mixed
     */
    public function changeStatus($id)
    {
        $invitation = $this->invitationRepository->findOrFail($id);

        if (strtolower($this->user->email) != strtolower($invitation->receiver_email)) {
            throw new ErrorResponseException(trans('api.You are not authorized to do actions in this invitation'));
        }

        if ($invitation->status != InvitationEnums::PENDING) {
            throw new ErrorResponseException(trans('api.Invitation is not pending to perform action'));
        }

        if (!in_array(request('status'), InvitationEnums::getReceiverStatusActions())) {
            throw new ErrorResponseException(trans('api.Unknown Action'));
        }

        $this->invitationRepository->update($invitation, ['status' => request('status')]);

        // case accepted
        if (request('status') == InvitationEnums::ACCEPTED) {
            if ($this->user->type == UserEnums::PARENT_TYPE) {
//                print("1   ".$invitation->sender_id);
//                dd($this->user);
                $this->user->students()->syncWithoutDetaching($invitation->sender_id);
            }

            if ($this->user->type == UserEnums::STUDENT_TEACHER_TYPE) {
                $pivot = StudentTeacherStudent::create([
                    'student_teacher_id' => $this->user->id,
                    'student_id' => $invitation->sender_id,
                    'status' => InvitationEnums::ACCEPTED,
                ]);

                $pivot->subjects()->sync($invitation->subjects);
            }

            if ($this->user->type == UserEnums::STUDENT_TYPE) {
                if ($invitation->sender->type == UserEnums::PARENT_TYPE) {
                    $this->user->parents()->syncWithoutDetaching($invitation->sender_id);
                }

                if ($invitation->sender->type == UserEnums::STUDENT_TEACHER_TYPE) {
                    StudentTeacherStudent::create([
                        'student_teacher_id' => $invitation->sender_id,
                        'student_id' => $this->user->id,
                        'status' => InvitationEnums::ACCEPTED,
                    ]);
                }
            }
        }

        return $invitation;
    }

    /**
     * Accept | Refuse invitation
     * @param integer $id
     * @return mixed
     */
    public function cancelInviation($id)
    {
        $invitation = $this->invitationRepository->findOrFail($id);

        if ($this->user->id != $invitation->sender_id) {
            throw new ErrorResponseException(trans('api.You dont own this invitation'));
        }

        if ($invitation->status != InvitationEnums::PENDING) {
            throw new ErrorResponseException(trans('api.Invitation is not pending to perform action'));
        }

        $this->invitationRepository->update($invitation, ['status' => InvitationEnums::CANCELED]);

        return $invitation;
    }


    /**
     * @param $email
     * @return void
     */
    protected function sendSuperviseMail(string $email, $invitation, $type)
    {
        $url = getDynamicLink(
            DynamicLinksEnum::STUDENT_DYNAMIC_URL,
            [
                'firebase_url' => env('FIREBASE_URL_PREFIX'),
                'portal_url' => env('STUDENT_PORTAL_URL'),
                'query_param' =>'invitation_id%3D'.$invitation->id.'%26target_screen%3D'.DynamicLinkTypeEnum::ACCEPT_INVITATION
            ]
        );

        $notificationData = [
            'emails' => $email,
            'mail' => [
                'user_type' => $type,
                'data' => ['url' => $url,
                    'lang' => App::getLocale()],
                'subject' => trans('notification.Our education invitation', [], App::getLocale()),
                'view' => 'invitationMail'
            ]
        ];

        $this->notifierFactory->send($notificationData);

        Cache::remember("{$this->user->id}_invited_{$email}", now()->addMinutes(env('INVITATION_CACHE_TIME', 2)), function () {
            return 1;
        });
    }

    protected function sendSuperviseNotifications($user, $invitation)
    {
        $url = getDynamicLink(
            DynamicLinksEnum::STUDENT_DYNAMIC_URL,
            [
                'firebase_url' => env('FIREBASE_URL_PREFIX'),
                'portal_url' => env('STUDENT_PORTAL_URL'),
                'query_param' =>'invitation_id%3D'.$invitation->id.'%26target_screen%3D'.DynamicLinkTypeEnum::ACCEPT_INVITATION,
                'android_apn' => env('ANDROID_APN','com.ouredu.students')
            ]
        );

        $notificationData = [
            'users' => new Collection([$user]),
            'mail' => [
                'user_type' => $user->type,
                'data' => ['url' => $url,
                'lang' => $user->language],
                'subject' => trans('notification.Our education invitation', [], $user->language),
                'view' => 'invitationMail'
            ],
            'fcm' => [
                'data' => [
                    'title' => buildTranslationKey('notification.Our education invitation'),
                    'body' => buildTranslationKey('notification.Our education invitation! check it out'),
                    'url' => $url,
                    'data' => [
                        'screen_type' => NotificationEnum::RECEIVED_INVITATION,
                        'inviter_type' => $this->user->type,
                        'invitation_id' => $invitation->id

                    ]
                ]
            ]
        ];

        $this->notifierFactory->send($notificationData);
        Cache::remember("{$this->user->id}_invited_{$user->email}", now()->addMinutes(env('INVITATION_CACHE_TIME', 2)), function () {
            return 1;
        });
    }

    protected function handleInvitationSending($email, $type, $subjects, $receiver = null)
    {
        $invitation = $this->invitationRepository->create(
            [
                'sender_id' => $this->user->id,
                'receiver_id' => $receiver?->id,
                'receiver_email' => $email,
                'type' => "{$this->user->type}_{$type}"
            ],
            $this->user
        );

        // only if student invites teacher
        if ($this->user->type == UserEnums::STUDENT_TYPE && $type == UserEnums::STUDENT_TEACHER_TYPE) {
            $invitation->subjects()->sync($subjects);
        }

//        $url = getDynamicLink(
//            DynamicLinksEnum::STUDENT_DYNAMIC_URL,
//            [
//                'firebase_url' => env('FIREBASE_URL_PREFIX'),
//                'portal_url' => env('STUDENT_PORTAL_URL'),
//                'query_param' =>'invitation_id%3D'.$invitation->id.'%26target_screen%3D'.DynamicLinkTypeEnum::ACCEPT_INVITATION
//            ]
//        );

        // create invitation and notify receiver
        if (!empty($receiver)) {
            $this->sendSuperviseNotifications($receiver, $invitation);

            return $invitation;
        }
        $this->sendSuperviseMail($email, $invitation, $type);
        return $invitation;
    }

    public function validate($request): array
    {
        $errors = [];

        if (!$request->has('type')) {
            $errors[] = [
                'status' => 422,
                'title' => 'type',
                'detail' => trans('validation.required', ['attribute' => trans('invitations.type')]),
            ];

            return $errors;
        }

        if (!$request->has('email')) {
            $errors[] = [
                'status' => 422,
                'title' => 'email',
                'detail' => trans('validation.required', ['attribute' => trans('app.email')]),
            ];

            return $errors;

        }
        $email = $this->userRepository->findByEmail(
            $request->get('email'),
            boolval($request->abilities_user) ?? false
        );

        if ($email && $email?->type !== $request->get('type')) {
            $errors[] = [
                'status' => 422,
                'title' => 'type',
                'detail' => trans('invitations.type_not_valid'),
            ];

            return $errors;

        }

        if ($request->has('type') and !empty($this->supportedTypes()) and !in_array(
                $request->get('type'),
                $this->supportedTypes()
            )) {
            $errors[] = [
                'status' => 422,
                'title' => 'type',
                'detail' => trans('invitations.type_not_valid'),
            ];

            return $errors;
        }

        return $errors;
    }

    private function supportedTypes():array
    {
        if ($this->user) {
            if ($this->user->type == UserEnums::STUDENT_TEACHER_TYPE) {
                return [UserEnums::STUDENT_TYPE];
            }

            if ($this->user->type == UserEnums::PARENT_TYPE) {
                return [UserEnums::STUDENT_TYPE];
            }

            if ($this->user->type == UserEnums::STUDENT_TYPE) {
                return [UserEnums::STUDENT_TEACHER_TYPE, UserEnums::PARENT_TYPE];
            }
        }

        return [];
    }
}
