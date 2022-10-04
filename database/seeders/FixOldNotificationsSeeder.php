<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\Notifications\Models\Notification;
use Illuminate\Support\Str;

class FixOldNotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Notification::select('data')
            ->where('type', 'App\OurEdu\BaseNotification\DataBaseNotification\DataBaseNotification')
            ->chunk(100, function ($notifications) {
                foreach ($notifications as $notification) {
                    $dataArr = $notification->data;
                    if (isset($dataArr['url'])) {
                        // join vcr notification
                        $oldVCRUrl = 'https://onlinelearning.testenv.tech/';
                        $prodVCRUrl = 'https://vcr.ta3lom.com/';
                        if (Str::contains($dataArr['url'], $oldVCRUrl)) {
                            dump('VCR URL Before: ');
                            dump($dataArr['url']);

                            $replaced = str_replace($oldVCRUrl, $prodVCRUrl, $dataArr['url']);
                            $dataArr['url'] = $replaced;
                            $notification->data = $dataArr;
                            $notification->save();

                            dump('VCR URL After: ');
                            dump($notification->data['url']);
                        }
                    }
                }
            });
    }
}
