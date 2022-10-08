<?php

namespace Database\Seeders;

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
        for ($i = 1; $i <= 1 ; $i++){
            $event = new Event;
            $event->title_ar = "عنوان $i";
            $event->title_en = "Title $i";
            $event->image = "/uploads/events/event-1664893285.png";
            $event->event_url = "https://google.com";
            $event->save();
        }
    }
}
