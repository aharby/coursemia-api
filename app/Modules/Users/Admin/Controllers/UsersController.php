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
}
