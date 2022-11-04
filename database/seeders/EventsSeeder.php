<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use App\Modules\Events\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

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
        $images = Storage::allFiles('uploads/large/events/');
        while ($index < 100) {
            $event = [
                'image' => str_replace('uploads/large/', '', $images[array_rand($images)]),
                'is_active' => 1,
                'event_url' => "https://google.com",
                'title:en' => "event title ${index} en",
                'title:ar' => "عنوان${index} ar"
            ];
            Event::create($event);
            $index++;
        }
    }
}
