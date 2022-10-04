<?php

use App\OurEdu\VCRSessions\General\Models\UserZoom;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use Illuminate\Database\Seeder;

class deleteUnstoredZoomUsersFromZoom extends Seeder
{
    use Zoom;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "users";
        $query = [
            "page_size" => 300,
        ];

        do {
            $zoomUsersResponse = $this->zoomGet($path, $query);
            $zoomUsersResponseArray = json_decode($zoomUsersResponse, true);

            $zoomUsers = $zoomUsersResponseArray['users'];

            if (isset($zoomUsersResponseArray['next_page_token'])) {
                $query["next_page_token"] = $zoomUsersResponseArray['next_page_token'];
            } else {
                unset($query["next_page_token"]);
            }

            foreach ($zoomUsers as $zoomUser) {
                $isExistedInDB = UserZoom::query()
                    ->where("zoom_id", "=", $zoomUser["id"])
                    ->exists();

                if(!$isExistedInDB and $zoomUser['type'] == 1 and $zoomUser['email'] != "Manassat@ikcedu.net") {
                    $deleteUserPath = "users/{$zoomUser["id"]}";

                    $this->zoomDelete($deleteUserPath, ['action' => 'delete']);
                }
            }

            dump("next_page_token: ", $query["next_page_token"]);
            dump("total_records: ", $zoomUsersResponseArray["total_records"]);
        }
        while (isset($query["next_page_token"]));
    }
}
