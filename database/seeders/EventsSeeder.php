<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use App\Modules\Events\Models\Event;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $index = 0;
        $images = [
            '1665268849bfeeclcxql.png',
            '1665269224bdcqwnmfqx.png',
            '1665270122xezggasmel.png',
            '1665318858titubyjtgf.png',
            '1665319970efweevvkth.png',
            '1665319986pmhfmmgqyz.png',
            '1665320145qvrbmgpuhc.png',
            '1665417650htifgjxjwv.png',
            '1665422803vsrlzcbgcw.png',
            '1666815291vxrtejprju.jpg',
        ];
        while ($index < 100) {
            $event = [
                'image' => 'events/' . $images[array_rand($images)],
                'is_active' => 1,
                'event_url' => "https://google.com",
                'title:en' => "event title ${index} en",
                'title:ar' => "event title ${index} ar"
            ];
            Event::create($event);
            $index++;
        }
    }
}
