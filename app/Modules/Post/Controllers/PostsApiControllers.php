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
use App\Modules\Post\Requests\Api\GetPostOwnerRequest;
use App\Modules\Post\Resources\Api\PostsCollection;
use App\Modules\Post\Resources\Api\PostsResource;
use App\Modules\Post\Resources\Api\PostUserResource;
use App\Modules\Users\Resources\UserResorce;
use App\Modules\Users\User;
use App\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostsApiControllers
{
    public function getPosts(Request $request)
    {
        $user = auth('api')->user();
        $posts = Post::query();
        if ($request->posts_type == 1){ // if type = recent
            $posts = $posts->orderBy('created_at', 'DESC');
        }
        if ($request->posts_type == 2){ // if type = 'top'
            $posts = $posts->withCount('likes')->orderByDesc('likes_count');
        }
        if (isset($user)) {
            if ($request->posts_type == 3) {  // if type == 'followed'
                $followed = UserFollow::where('follower_id', $user->id)->pluck('followed_id')->toArray();
                $posts = $posts->whereIn('user_id', $followed);
            }
        }
        else
            return customResponse((object)[], "You are not logged in to view followers posts", 422, StatusCodesEnum::FAILED);
        $posts = $posts->paginate(request()->perPage, ['*'], 'page', request()->page);
        return customResponse(new PostsCollection($posts), "Done", 200, StatusCodesEnum::DONE);
    }

    public function getPost($id){
        $post = Post::with('comments')->find($id);
        return customResponse(new PostsResource($post), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function store(StorePostRequest $request){
        $user = auth('api')->user();
        $content = $request->get('content');
        $image = $request->image;
        if (isset($image))
            $image = $this->upload($image);
        $video = $request->video;
        if (isset($video))
            $video = $this->upload($video);
        $file = $request->file;
        if (isset($file))
            $file = $this->upload($file);
        DB::beginTransaction();
        try {
            $post = new Post;
            $post->user_id = $user->id;
            $post->content = $content;
            $post->image = $image;
            $post->video = $video;
            $post->file = $file;
            $post->save();
            if (sizeof($request->hashtags) > 0){
                foreach ($request->hashtags as $hashtag){
                    $postHashtag = new PostHashtag();
                    $postHashtag->post_id = $post->id;
                    $postHashtag->hashtag = $hashtag;
                    $postHashtag->save();
                }
            }
            DB::commit();
            return customResponse(new PostsResource($post), 'Post added successfully', 200, StatusCodesEnum::DONE);
        }catch (\Exception $e){
            DB::rollBack();
            return customResponse(null, $e->getMessage(), 500, StatusCodesEnum::FAILED);
        }
    }

    public function getPostOwner(GetPostOwnerRequest $request){
        $postUser = $request->user_id;
        $user = User::find($postUser);
        return customResponse(new PostUserResource($user), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function getMyPosts(Request $request){
        $user = auth('api')->user();
        $posts = Post::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(request()->perPage, ['*'], 'page', request()->page);
        return customResponse(new PostsCollection($posts), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function addComment(AddCommentRequest $request){
        $user = auth('api')->user();
        $comment = $request->comment;
        $post_id = $request->post_id;
        $postComment = new PostComment;
        $postComment->post_id = $post_id;
        $postComment->comment = $comment;
        $postComment->user_id = $user->id;
        $postComment->save();
        return customResponse((object)[], 'Done', 200, StatusCodesEnum::DONE);
    }

    public function addLike(AddLikeRequest $request){
        $user = auth('api')->user();
        $type = $request->type;
        $post_id = $request->post_id;
        $action = PostLike::where([
            'type' => $type,
            'user_id' => $user->id
        ])->first();
        if (!$action){
            $postLike = new PostLike();
            $postLike->post_id = $post_id;
            $postLike->type = $type;
            $postLike->user_id = $user->id;
            $postLike->save();
        }else{
            $action->delete();
        }
        return customResponse((object)[], 'Done', 200, StatusCodesEnum::DONE);
    }

    public function upload($file)
    {
        $fileExtension = trim($file->getClientOriginalExtension());
        $fileName = strtolower(Str::random(10).trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file->getClientOriginalName()), '-')) . '.' . $fileExtension;
        $location = 'public/uploads/posts';
        $path = $file->storeAs(
            $location, $fileName
        );
        return 'storage' . '/' . $path;
    }
}
