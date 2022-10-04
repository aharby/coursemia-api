<?php

namespace Database\Seeders;

use App\OurEdu\VCRSessions\General\Models\UserZoom;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use Illuminate\Database\Seeder;

class DeleteZoomUsers extends Seeder
{
    use Zoom;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = UserZoom::query()->get();

        foreach ($users as $user) {
            $delete = $this->deleteZoomUser($user->zoom_id);
            if ($delete == 204){
                $user->delete();
            }

        }
    }

    private function deleteZoomUser($zoomUserId)
    {
        $deleteUserPath = "users/{$zoomUserId}?action=delete&&transfer_meeting=false&&transfer_webinar=false&&transfer_recording=false";

        $delete = $this->zoomDelete($deleteUserPath);
        dump($delete->body(),$delete->status(),$zoomUserId);
        return $delete->status();
    }
}
