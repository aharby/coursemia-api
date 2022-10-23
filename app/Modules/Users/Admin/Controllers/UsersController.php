<?php


namespace App\Modules\Users\Admin\Controllers;

use App\Enums\StatusCodesEnum;
use App\Modules\Users\Admin\Models\Admin;
use App\Modules\Users\Admin\Resources\AdminResource;
use App\Modules\Users\Admin\Resources\UsersResource;
use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use App\Modules\BaseApp\Controllers\AjaxController;
use App\Modules\Users\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends AjaxController
{
    public function index(){
        $users = User::paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $users->total(),
            'users' => UsersResource::collection($users->items())
        ]);
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if ($request->has('is_active')) {
            $user->is_active = $request->get('is_active');
        }
        $user->save();
        return customResponse('', trans('api.Updated Successfully'), 200,StatusCodesEnum::DONE);
    }

    public function show($id){
        $user = User::find($id);
        return customResponse(new UsersResource($user), trans('api.Updated successfully'), 200, StatusCodesEnum::DONE);
    }
}
