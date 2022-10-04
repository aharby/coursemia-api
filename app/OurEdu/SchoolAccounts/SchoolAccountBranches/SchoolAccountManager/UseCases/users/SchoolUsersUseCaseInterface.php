<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\users;


use App\OurEdu\Users\User;
use Illuminate\Http\Request;

interface SchoolUsersUseCaseInterface
{
    public function create(Request $request) : User;
}
