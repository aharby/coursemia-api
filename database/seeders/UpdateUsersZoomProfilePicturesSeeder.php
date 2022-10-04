<?php

namespace Database\Seeders;

use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSessions\Models\ZoomHost;
use Illuminate\Database\Seeder;

class UpdateUsersZoomProfilePicturesSeeder extends Seeder
{

    private CreateZoomUserUseCaseInterface $createZoomUser;

    public function __construct(CreateZoomUserUseCaseInterface $createZoomUser)
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
            ->with('zoom')
            ->whereHas('zoom')
            ->get();

        foreach ($users  as $user) {
            $this->createZoomUser->changeProfilePicture($user->zoom->zoom_id, $user->profile_picture);
        }

        $zoomHosts = ZoomHost::query()->get();

        foreach ($zoomHosts  as $host) {
            $this->createZoomUser->changeHostProfileImage($host->zoom_user_id);
        }
    }
}
