<?php

namespace App\OurEdu\Profile\Admin\Controllers;

use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Users\Events\UserModified;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Profile\Admin\Requests\ProfileRequest;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\UpdateProfileUseCase\UpdateProfileUseCaseInterface;

class ProfileController extends BaseController
{
    public $model;
    public $module;
    private $parent;
    private $repository;

    public function __construct(UserRepositoryInterface $user, UpdateProfileUseCaseInterface $updateProfileUseCase)
    {
        $this->module = 'profile';
        $this->repository = $user;
        $this->useCase = $updateProfileUseCase;
        $this->parent = ParentEnum::ADMIN;
    }

    public function getEdit()
    {
        $langs = array();
        foreach (config("translatable.locales") as $lang) {
            $langs[$lang] = trans('app.'.$lang);
        }
        $data['row'] = $this->repository->findOrFail(auth()->user()->id);
        $data['languages'] = $langs;

        $data['page_title'] = trans('profile.Account');
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function postEdit(ProfileRequest $request)
    {
        $useCase = $this->useCase->updateProfile($request->all(), $this->repository);
        if ($useCase['code'] == 200) {
            flash($useCase['message'])->success();
            UserModified::dispatch($request->except( '_token', '_method'), Auth::user()->toArray(), 'User updated profile');

            return back();
        } else {
            flash(trans($useCase['message']))->error();
            return back()->withInput();
        }
    }
}
