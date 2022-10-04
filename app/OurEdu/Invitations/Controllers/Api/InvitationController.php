<?php

namespace App\OurEdu\Invitations\Controllers\Api;

use App\OurEdu\Users\User;
use Illuminate\Http\Request;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\OurEduErrorException;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Users\Transformers\UserAuthTransformer;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Invitations\Repository\InvitationRepository;
use App\OurEdu\Invitations\Transformers\InvitationTransformer;
use App\OurEdu\Invitations\Requests\SearchForInvitationRequest;
use App\OurEdu\Invitations\Student\Requests\Api\InvitationRequest;
use App\OurEdu\Invitations\Repository\InvitationRepositoryInterface;
use App\OurEdu\Invitations\UseCases\SuperviseInvitationUseCaseInterface;

class InvitationController extends BaseApiController
{
    protected $user;
    protected $userRepository;
    protected $parserInterface;
    protected $superviseInvitationUseCase;
    protected $inviationRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ParserInterface $parserInterface,
        SuperviseInvitationUseCaseInterface $superviseInvitationUseCase,
        InvitationRepositoryInterface $inviationRepository
    ) {
        $this->middleware('auth:api')->except('changeStatus');

        $this->user = Auth::guard('api')->user();
        $this->userRepository = $userRepository;
        $this->parserInterface = $parserInterface;
        $this->superviseInvitationUseCase = $superviseInvitationUseCase;
        $this->inviationRepository = $inviationRepository;

        $this->middleware('type:parent|student|student_teacher')->except('changeStatus');
    }

    /**
     * Accept | Refuse inviation
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function changeStatus($id)
    {
        if (!$this->user){
            return formatErrorValidation([
                [
                    'status' =>  422,
                    'title' => 'unauthorized_action',
                    'detail' =>trans('api.You need to be registered and login to do actions in this invitation , login first'),
                ]
            ],422);
        }
        $invitation = $this->superviseInvitationUseCase->changeStatus($id);
        $params['no_action'] = true;

        return $this->transformDataModInclude(
            $invitation,
            [
                'sender',
                'receiver',
                'invitable'
            ],
            new InvitationTransformer($params),
            ResourceTypesEnums::INVITATION,
            [
                'meta' => [
                    'message' => trans('api.Invitation status updated')
                ]
            ]
        );
    }

    /**
     * List invitations
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $invitations = $this->user->invitations()
                ->with('receiver')
                ->where('invitable_type', 'user')
                ->jsonPaginate(env('PAGE_LIMIT', 20));

            return $this->transformDataModInclude($invitations, ['receiver', 'actions', 'subjects'], new InvitationTransformer(), ResourceTypesEnums::INVITATION);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /**
     * Search parents to send them supervise invitation
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function search(SearchForInvitationRequest $request)
    {
        try {
            $userType = auth()->user()->type;

            $users = $this->userRepository->searchUsersByEmailAndType($request->q, $request->type);

            if ($users->isEmpty()) {
                $unregisteredUser = new User();
                $unregisteredUser->id;
                $unregisteredUser->type = ResourceTypesEnums::UNREGISTERED_USER;
                $unregisteredUser->email = $request->q;

                $users = [
                    $unregisteredUser
                ];

                $meta = [
                    'message' => trans('app.No data found')
                ];

                $params['no_data'] = true;

                $include = 'actions';

                return $this->transformDataModInclude($users, $include, new UserAuthTransformer($params), ResourceTypesEnums::UNREGISTERED_USER, $meta);
            }

            $params['invitations_search'] = true;
            $include = 'actions';
            return $this->transformDataModInclude($users, $include, new UserAuthTransformer($params), ResourceTypesEnums::USER);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /**
     * Invite parent to supervise a student
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function invite(InvitationRequest $request)
    {

        $email = $request->email;
        $type = $request->type;
        $subjects = $request->data['attributes']['subjects'] ?? [];
        $abilitiesUser = boolval($request->abilities_user)  ?? false;

        $error = $this->superviseInvitationUseCase->validate($request);

         if(count($error)){

             return formatErrorValidation($error,422);
         }

        $invitation = $this->superviseInvitationUseCase->checkIfSentBeforeWherePending($email);

        if ($invitation) {
            $invitation = $this->superviseInvitationUseCase->resendSuperviseInvitation($invitation->id, $type, $abilitiesUser);
        } else {
            $invitation = $this->superviseInvitationUseCase->superviseInvitation($email, $type, $subjects,$abilitiesUser);
        }

        $params = [];

        return $this->transformDataModInclude(
            $invitation,
            [
                'sender',
                'receiver',
                'subjects',
            ],
            new InvitationTransformer($params),
            ResourceTypesEnums::INVITATION,
            [
                'meta' => [
                    'message' => trans('api.Invitation sent')
                ]
            ]
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function resendInvite(Request $request, $id)
    {
        $invitation = $this->inviationRepository->findOrFail($id);

        $subbedLength = strlen($this->user->type) + 1;

        $type = substr($invitation->type, $subbedLength);
        $abilitiesUser = boolval($request->abilities_user)  ?? false;

        if ($invitation->status != InvitationEnums::PENDING) {
            throw new ErrorResponseException(trans('invitations.Invitation status must be pending to resend'));
        }

        $invitation = $this->superviseInvitationUseCase->resendSuperviseInvitation($id, $type, $abilitiesUser);

        $params['no_action'] = true;
        return $this->transformDataModInclude(
            $invitation,
            [
                'sender',
                'receiver',
                'subjects',
            ],
            new InvitationTransformer($params),
            ResourceTypesEnums::INVITATION,
            [
                'meta' => [
                    'message' => trans('api.Invitation resent')
                ]
            ]
        );
    }

    public function cancelInviation($id)
    {
        $invitation = $this->superviseInvitationUseCase->cancelInviation($id);

        return $this->transformDataModInclude(
            $invitation,
            [
                'sender',
                'receiver',
                'subjects',
            ],
            new InvitationTransformer(),
            ResourceTypesEnums::INVITATION,
            [
                'meta' => [
                    'message' => trans('api.Invitation canceled')
                ]
            ]
        );
    }
}
