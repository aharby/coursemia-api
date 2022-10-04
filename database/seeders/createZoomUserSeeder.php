<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\Users\User;
use \App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;

class createZoomUserSeeder extends Seeder
{
    private $createZoomUser;

    public function __construct(CreateZoomUserUseCaseInterface  $createZoomUser)
    {
        $this->createZoomUser = $createZoomUser;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        $users  = User::query()
            ->whereDoesntHave('zoom')
            ->whereIn('type',UserEnums::allowedUserUsingZoom())
            ->where('is_active',1)
            ->get();
        foreach ($users  as $user){
            $this->createZoomUser->createUser($user);
        }

    }
}
