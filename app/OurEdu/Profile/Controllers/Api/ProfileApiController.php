<?php

namespace App\OurEdu\Profile\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Invitations\UseCases\SuperviseInvitationUseCaseInterface;
use App\OurEdu\Profile\Middleware\ParentChildMiddleware;
use App\OurEdu\Profile\Requests\Api\UpdateLanguageRequest;
use App\OurEdu\Profile\Requests\Api\UpdatePasswordRequest;
use App\OurEdu\Profile\Requests\Api\UpdateProfileRequest;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Events\UserModified;
use App\OurEdu\Users\Repository\FirebaseTokenRepository;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\Transformers\ParentTransformer;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Users\UseCases\UpdateProfileUseCase\UpdateProfileUseCaseInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Throwable;

class ProfileApiController extends BaseApiController
{
    protected $user;
    protected $superviseInvitationUseCase;
    private $userRepository;
    private $updateProfileUseCase;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ParserInterface $parserInterface,
        UpdateProfileUseCaseInterface $updateProfileUseCase,
        SuperviseInvitationUseCaseInterface $superviseInvitationUseCase,
        public FirebaseTokenRepository $firebaseRepository,
        public TokenManagerInterface $tokenManager,

    )
    {
        $this->userRepository = $userRepository;
        $this->parserInterface = $parserInterface;
        $this->updateProfileUseCase = $updateProfileUseCase;
        $this->superviseInvitationUseCase = $superviseInvitationUseCase;
        $this->user = Auth::guard('api')->user();

        $this->middleware('type:student')
            ->only(['viewParentProfile', 'listParents']);
        $this->middleware('type:parent')
            ->only(['viewChildProfile', 'listStudents']);

        $this->middleware(ParentChildMiddleware::class)->only(['viewChildProfile']);

    }

    public function getProfile(BaseApiParserRequest $request)
    {
        $user = $this->user;

        $include = '';
        $params = [];

        if ($request->filled('subjects_limit') && $request->subjects_limit == "false") {
            $params['subjects_limit'] = true;
        }

        if ($request->filled('courses_limit') && $request->courses_limit == "false") {
            $params['courses_limit'] = true;
        }

        if ($request->filled('sessions_limit') && $request->sessions_limit == "false") {
            $params['sessions_limit'] = true;
        }

        if ($user->type == UserEnums::STUDENT_TYPE) {
            $include = $request->include ?? 'student';
        }

        if ($user->type == UserEnums::PARENT_TYPE) {
            $include = $request->include ?? 'parent';
        }

        if ($user->type == UserEnums::INSTRUCTOR_TYPE) {
            $include = $request->include ?? 'instructor';
        }

        if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            $include = $request->include ?? 'contentAuthor';
        }

        if ($user->type == UserEnums::STUDENT_TEACHER_TYPE) {
            $include = $request->include ?? 'studentTeacher';
        }

        $include = [$include, 'actions'];

        return $this->transformDataModInclude($user, $include, new UserTransformer($params), ResourceTypesEnums::USER);
    }

    public function postUpdateProfile(UpdateProfileRequest $request)
    {
        $row = $this->user;
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $update = [];
        if (!($row->type == UserEnums::STUDENT_TYPE && $row->username != null) ) {
            $update['first_name'] = $data->first_name;
            $update['last_name'] = $data->last_name;
        }
        
        $update['language'] = $data->language;
        $update['mobile'] = $data->mobile;
        $update['email'] = $data->email;

        if (isset($data->attach_media) && !is_null($data->attach_media)) {
            $update['profile_picture'] = $data->attach_media->first()->getId();
        }
        if (isset($data->old_password) && !is_null($data->old_password) && !$row->added_by_social) {
            $update['old_password'] = $data->old_password;
        }
//            if(isset()&&!is_null($data->password)) {
//                $update['password'] = $data->password;
//            }
        if (isset($data->birth_date) && !is_null($data->birth_date)) {
            $update['birth_date'] = $data->birth_date;
        }
        if (isset($data->educational_system) && !is_null($data->educational_system)) {
            $update['educational_system'] = $data->educational_system;
        }

        if (isset($data->school) && !is_null($data->school)) {
            $update['school'] = $data->school;
        }
        if (isset($data->class) && !is_null($data->class)) {
            $update['class'] = $data->class;
        }
        if (isset($data->academical_year) && !is_null($data->academical_year)) {
            $update['academical_year'] = $data->academical_year;
        }
        if (isset($data->country) && !is_null($data->country)) {
            $update['country_id'] = $data->country;
        }

        if ($row->type == UserEnums::INSTRUCTOR_TYPE) {
            if (isset($data->school_id) && !is_null($data->school_id)) {
                $update['school_id'] = $data->school_id;
            }
        }

        $update = $this->updateProfileUseCase->updateProfile($update, $this->userRepository);
        if ($update['code'] == 200) {
            $include = '';
            if ($update['user']->type == UserEnums::STUDENT_TYPE) {
                $include = $request->include ?? 'student';
            }

            if ($update['user']->type == UserEnums::PARENT_TYPE) {
                $include = $request->include ?? 'parent';
            }

            if ($update['user']->type == UserEnums::INSTRUCTOR_TYPE) {
                $include = $request->include ?? 'instructor';
            }

            if ($update['user']->type == UserEnums::CONTENT_AUTHOR_TYPE) {
                $include = $request->include ?? 'contentAuthor';
            }

            UserModified::dispatch($data->toArray(), $this->user->toArray(), 'User updated profile');
            return $this->transformDataModInclude($update['user'], $include, new UserTransformer(), ResourceTypesEnums::USER, ['message' => $update['message']]);

        } else {
            $error = [
                'status' => $update['code'],
                'title' => $update['title'],
                'detail' => $update['message']
            ];
            return formatErrorValidation($error, $update['code']);
        }
//        } catch (\Throwable $e) {
//            Log::error($e);
//            throw new OurEduErrorException($e->getMessage());
//        }
    }

    public function postUpdatePassword(UpdatePasswordRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            $update = [];
            $update['old_password'] = $data->old_password;
            $update['password'] = $data->password;

            $update = $this->updateProfileUseCase->updatePassword($update, $this->userRepository);
            if ($update['code'] == 200) {
                UserModified::dispatch($data->toArray(), $this->user->toArray(), 'User updated profile');
                $meta = [
                    'message' => trans('profile.Updated successfully')
                ];

                return response()->json(['meta' => $meta], 200);
            } else {
                $error = [
                    'status' => $update['code'],
                    'title' => $update['title'],
                    'detail' => $update['message']
                ];
                return formatErrorValidation($error, $update['code']);
            }
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function postUpdateLanguage(UpdateLanguageRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            $update['language'] = $data->language;
            $update = $this->updateProfileUseCase->updateLanguage($update, $this->userRepository);
            if ($update['code'] == 200) {
                $include = '';
                if ($update['user']->type == UserEnums::STUDENT_TYPE) {
                    $include = $request->include ?? 'student';
                }

                if ($update['user']->type == UserEnums::PARENT_TYPE) {
                    $include = $request->include ?? 'parent';
                }

                if ($update['user']->type == UserEnums::INSTRUCTOR_TYPE) {
                    $include = $request->include ?? 'instructor';
                }

                if ($update['user']->type == UserEnums::CONTENT_AUTHOR_TYPE) {
                    $include = $request->include ?? 'contentAuthor';
                }

                UserModified::dispatch($data->toArray(), $this->user->toArray(), 'User updated profile');

                return $this->transformDataModInclude($update['user'], $include, new UserTransformer(), ResourceTypesEnums::USER, ['message' => $update['message']]);
            } else {
                $error = [
                    'status' => $update['code'],
                    'title' => $update['title'],
                    'detail' => $update['message']
                ];
                return formatErrorValidation($error, $update['code']);
            }
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function viewParentProfile($parentId)
    {
        try {
            $user = $this->userRepository->findOrFail($parentId);
            return $this->transformDataModInclude($user, '', new UserTransformer(), ResourceTypesEnums::USER);
        } catch (Throwable $e) {
            throw new OurEduErrorException($e->getMessage());
        }
    }

    // not working until its business is finished
    public function viewChildProfile($childId)
    {
        try {
            $user = $this->userRepository->findOrFail($childId);
            $include = ['student.subjects', 'actions'];
            return $this->transformDataModInclude($user, $include, new UserTransformer(), ResourceTypesEnums::USER);
        } catch (Throwable $e) {
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listParents()
    {
        try {
            $parents = $this->user->parents;

            if ($parents->isEmpty()) {
                $unregisteredUser = new User();
                $parents = [
                    $unregisteredUser
                ];
                $params['no_data'] = true;
                $meta = [
                    'message' => trans('app.No Parents found')
                ];
                $include = ['invitedParents', 'invitedParents.actions'];
                return $this->transformDataModInclude($parents, $include, new ParentTransformer($params), ResourceTypesEnums::INVITED_PARENTS, $meta);
            }

            $include = ['actions', 'invitedParents', 'invitedParents.actions'];
            return $this->transformDataModInclude($parents, $include, new ParentTransformer(), ResourceTypesEnums::USER);
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listStudents()
    {
        try {
            $students = $this->user->students()->jsonPaginate();
            $include = ['student.actions', 'student.invitedStudents.actions', 'actions'];
            return $this->transformDataModInclude($students, $include, new UserTransformer(), ResourceTypesEnums::USER);
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function removeRelation($id)
    {
        $user = $this->userRepository->findOrFail($id);
        $invitation = $this->superviseInvitationUseCase->removeRelation($user->id);

        return response()->json([
            'meta' => [
                'message' => trans('api.Updated Successfully')
            ]
        ]);
    }

    public function deleteProfile()
    {
        try {
            $user = $this->user;
            $this->firebaseRepository->delete($user, []);

            $this->tokenManager->revokeAuthAccessToken();

            $data = [
                'deleted_ios_action' => 1
            ];
            $this->updateProfileUseCase->updateProfile($data, $this->userRepository);
            return response()->json([
                'meta' => [
                    'message' => trans('api.Profile Deleted Successfully')
                ]
            ]);
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
}
