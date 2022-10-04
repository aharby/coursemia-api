<?php

namespace Database\Seeders;

use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\VCRSessions\Enums\ZoomHostStatusEnum;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use App\OurEdu\VCRSessions\Models\ZoomHost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CreateZoomHostsSeeder extends Seeder
{
    use Zoom;

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
        for ($i =0; $i < 100; $i++) {
            $account = $this->zoomPost(
                'users',
                [
                    'action' => 'custCreate',
                    'user_info' => [
                        'email' => Str::random(8) . time() . '_manasat@ikcedu.net',
                        'type' => 2,
                        'first_name' => Str::random(5),
                        'last_name' => Str::random(5)
                    ]
                ]
            );

            if ($account->status() == 201) {
                $account = json_decode($account->body(), true);
                ZoomHost::query()->create(
                    [
                    'zoom_user_id' => $account['id'],
                    'usage_status' => ZoomHostStatusEnum::AVAILABLE,
                    ]
                );
                $this->createZoomUser->changeHostProfileImage($account['id']);
            }
        }
    }
}
