<?php
namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\SchoolSupervisor\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassroomClassSessionController extends BaseController
{

    private $notifierFactory;
    /**
     * @var string
     */
    private $module;
    /**
     * @var array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|mixed|string|null
     */
    private $title;
    private $parent;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;


    public function __construct(NotifierFactoryInterface $notifierFactory, TokenManagerInterface $tokenManager)
    {
        $this->module = 'classroomClassSessions';
        $this->title = trans('app.Classroom Sessions');
        $this->notifierFactory = $notifierFactory;
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->tokenManager = $tokenManager;
    }

    public function getIndex($classroomClass)
    {
        authorize('view-sessions');

        $data['rows'] = ClassroomClassSession::with("vcrSession")
            ->where('classroom_class_id', $classroomClass)
            ->paginate();

        $data['page_title'] = trans('app.List Sessions');

        $data['parent'] = $this->parent;
        $data['classroomClass'] = $classroomClass;
        return view('school_supervisor.classroomClassSessions.index', $data);
    }

    public function getEdit($classroomClassSession, SchoolAccountBranch $branch)
    {
        authorize('update-sessions');
        $data['page_title'] = trans('app.Edit Sessions');

        $user = auth()->user();

        $classroomClassSession = ClassroomClassSession::findOrFail($classroomClassSession);
        $data['classroomClassSession'] = $classroomClassSession;
        $selectedSubject = $data['classroomClassSession']->subject;
        $data['instructors'] = User::where('type', UserEnums::SCHOOL_INSTRUCTOR)->where('id', '!=', $data['classroomClassSession']->instructor_id)->where('branch_id', $classroomClassSession->classroom->branch_id)->get()->pluck('name', 'id');
        $data['parent'] = $this->parent;
        return view('school_supervisor.classroomClassSessions.edit', $data);
    }

    public function postEdit($classroomClassSession, Request $request)
    {
        authorize('update-sessions');

        $classroomClassSession = ClassroomClassSession::findOrFail($classroomClassSession);

        $instructorSessions = ClassroomClassSession::query()
            ->where("instructor_id", "=", $request->get("instructor_id"))
            ->where("from", "<=", $classroomClassSession->to)
            ->where("to", ">=", $classroomClassSession->from)
            ->count();

        if ($instructorSessions) {
            return redirect()->back()->with(['error' => trans('classroomClass.This instructor has a session at the same time')]);
        }

        // temporary condition
        if ($request->instructor_id != $classroomClassSession->instructor_id) {
            $classroomClassSession->update($request->all());
            $vcrSession = $classroomClassSession->vcrSession;
            $vcrSession->update(['instructor_id' => $request->instructor_id]);
            $this->notifyNewInstructor($request->instructor_id, $classroomClassSession);
        }
        return redirect()->back()->with(['success' => trans('classroomClass.Updated Successfully')]);
    }

    private function notifyNewInstructor($instructorId, $classroomClassSession)
    {
        $vcrSession = $classroomClassSession->vcrSession;
        $instructorUser = User::findOrFail($instructorId);
        $token = $this->tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token, $instructorUser);
        $url = getDynamicLink(
            DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
            ['session_id' => $vcrSession->id, 'token' => $token,
                'type' => $vcrSession->vcr_session_type,
                'portal_url' => env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com')
            ]
        );

        $notificationData = [
            'users' => collect([$instructorUser]),
            NotificationEnums::MAIL => [
                'user_type' => UserEnums::SCHOOL_INSTRUCTOR,
                'data' => ['url' => $url, 'lang' => $instructorUser->language],
                'subject' => trans('notification.supervisor_assigned_you_to_session', [], $instructorUser->language),
                'view' => 'newVCRSessionAssigned'
            ],
            NotificationEnums::FCM => [
                'data' => [
                    'title' => buildTranslationKey('notification.supervisor_assigned_you_to_session'),
                    'body' => buildTranslationKey('notification.supervisor_assigned_you_to_session'),
                    'url' => $url,
                    'data' => [
                        'screen_type' => NotificationEnum::NOTIFY_INSTRUCTOR_ABOUT_NEW_SESSION_ASSIGNED,
                    ],
                ],
            ]
        ];
        $this->notifierFactory->send($notificationData);
    }

    public function delete(ClassroomClassSession $classroomClassSession)
    {
        if ((new Carbon($classroomClassSession->from))->isBetween(now(), now()->addMinutes(30))) {
            return back()->withErrors(trans('app.can not delete session will start within 30 min'));
        }
        $classroomClassSession->vcrSession()->delete();
        $classroomClassSession->delete();
        return back()->with(['success'=>trans('app.Deleted Successfully')]);
    }
}
