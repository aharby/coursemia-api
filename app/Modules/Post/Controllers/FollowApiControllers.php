<?php

namespace App\Modules\Post\Controllers;

use App\Enums\StatusCodesEnum;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Countries\Resources\Api\ListCountriesIndex;
use App\Modules\Countries\Resources\Api\ListCountriesIndexPaginator;
use App\Modules\Courses\Requests\Api\AddCommentRequest;
use App\Modules\Courses\Requests\Api\AddLikeRequest;
use App\Modules\Courses\Requests\Api\StorePostRequest;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Models\PostComment;
use App\Modules\Post\Models\PostHashtag;
use App\Modules\Post\Models\PostLike;
use App\Modules\Post\Requests\Api\AcceptFollowRequest;
use App\Modules\Post\Requests\Api\FollowUnFollowUserRequest;
use App\Modules\Post\Requests\Api\GetPostOwnerRequest;
use App\Modules\Post\Resources\Api\FollowRequestsResource;
use App\Modules\Post\Resources\Api\PostsCollection;
use App\Modules\Post\Resources\Api\PostsResource;
use App\Modules\Post\Resources\Api\PostUserResource;
use App\Modules\Users\Resources\UserResorce;
use App\Modules\Users\User;
use App\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FollowApiControllers
{
    public function followUnFollowUser(FollowUnFollowUserRequest $request){
        $user = auth('api')->user();
        $userToFollow = $request->user_id;
        $userFollow = UserFollow::where([
            'follower_id'   => $user->id,
            'followed_id'   => $userToFollow
        ])->first();
        if (isset($userFollow)){
            $userFollow->delete();
            return customResponse((object)[], trans('api.UnFollowed successfully'), 200, StatusCodesEnum::DONE);
        }else{
            $userFollow = new UserFollow;
            $userFollow->follower_id = $user->id;
            $userFollow->followed_id = $userToFollow;
            $userFollow->save();
            return customResponse((object)[], trans('api.Followed successfully'), 200, StatusCodesEnum::DONE);
        }
    }

    public function acceptRejectFollowRequest(AcceptFollowRequest $request){
        $user = auth('api')->user();
        $type = $request->type;
        $followRequest = UserFollow::where(['id' => $request->follow_id, 'status' => 0, 'followed_id' => $user->id])->first();
        if (isset($followRequest)){
            if ($type == 0){
                $followRequest->delete();
                return customResponse((object)[], trans('api.Follow rejected successfully'), 200, StatusCodesEnum::DONE);
            }
            $followRequest->status = 1;
            $followRequest->save();
            return customResponse((object)[], trans('api.Follow accepted successfully'), 200, StatusCodesEnum::DONE);
        }
        return customResponse((object)[], trans('api.Follow request not found'), 404, StatusCodesEnum::DONE);
    }

    public function getFollowRequestsList(){
        $user = auth('api')->user();
        $requests = UserFollow::where(['followed_id' => $user->id, 'status' => 0])->get();
        return customResponse(FollowRequestsResource::collection($requests), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function getMyFollowers(){
        $user = auth('api')->user();
        $requests = UserFollow::where(['followed_id' => $user->id, 'status' => 1])->get();
        return customResponse(FollowRequestsResource::collection($requests), 'Done', 200, StatusCodesEnum::DONE);
    }
}
