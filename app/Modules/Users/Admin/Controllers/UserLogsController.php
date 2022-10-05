<?php

namespace App\Modules\Users\Admin\Controllers;

use App\Modules\Users\Models\Student;
use App\Modules\Users\User;
use FontLib\Table\Type\name;
use App\Modules\Users\UserEnums;
use App\Modules\Helpers\MailManger;
use App\Http\Controllers\Controller;
use App\Modules\BaseApp\Helpers\Mail;
use App\Modules\BaseApp\Enums\ParentEnum;
use App\Modules\Users\Events\UserCreated;
use App\Modules\BaseApp\Helpers\MailClass;
use App\Modules\Users\Events\UserModified;
use App\Modules\Users\Requests\CreateUserRequest;
use App\Modules\Users\Requests\UpdateUserRequest;
use App\Modules\Users\Admin\Jobs\SendRegisterEMail;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\Repository\UserLogsRepositoryInterface;
use App\Modules\Users\Admin\Requests\InsturctorStudentsRequest;
use App\Modules\Users\Repository\InstructorRepositoryInterface;
use App\Modules\Users\Repository\ContentAuthorRepositoryInterface;
use App\Modules\Users\UseCases\CreateUserUseCase\CreateUserUseCaseInterface;
use App\Modules\Users\UseCases\UpdateUserUseCase\UpdateUserUseCaseInterface;
use App\Modules\Users\UseCases\SuspendUserUseCase\SuspendUserUseCaseInterface;

class UserLogsController extends Controller
{
    private $title;
    private $module;
    private $userRepository;
    private $logsRepository;
    private $parent;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserLogsRepositoryInterface $logsRepository
    ) {
        $this->module = 'users';
        $this->title = trans('app.Users');
        $this->userRepository = $userRepository;
        $this->logsRepository = $logsRepository;
        $this->parent = ParentEnum::ADMIN;
    }

    public function listUserLogs($id)
    {

        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[User::class,'user'])
           ->where('auditable_id',$id)
            ->where('old_values', '!=', '[]')
            ->where('new_values', '!=', '[]')
            ->orWhere(function ($query) use ($id){
                $query->where('auditable_type', Student::class)
                    ->where('tags', $id);
            })
            ->orWhere(function ($query) use ($id){
                $query->where('event', 'deleted')
                       ->where('auditable_id',$id);
            })
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List User Logs');
        $data['breadcrumb'] = [trans('navigation.Users') => route('admin.users.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

    public function viewUserLog($id)
    {
        $data['row'] = $this->logsRepository->findOrFail($id);
        $data['user'] = $data['row']->user;
        $data['row']->by = User::find($data['row']->event_properties['by']);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.View User Log');
        $data['breadcrumb'] = [trans('navigation.Users') => route('admin.users.get.index')];
        return view($this->parent . '.' . $this->module . '.viewLog', $data);
    }
}
