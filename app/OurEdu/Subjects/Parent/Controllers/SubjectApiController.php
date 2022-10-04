<?php

namespace App\OurEdu\Subjects\Parent\Controllers;

use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;

class SubjectApiController extends BaseApiController
{
    protected $userRepository;
    protected $user;

    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->repository = $subjectRepository;
        $this->userRepository = $userRepository;
        $this->user = Auth::guard('api')->user();
    }
}
