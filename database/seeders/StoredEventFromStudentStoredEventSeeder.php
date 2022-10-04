<?php

namespace Database\Seeders;

use App\OurEdu\Events\Models\StudentStoredEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function GuzzleHttp\json_encode;

class StoredEventFromStudentStoredEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studentEvents = StudentStoredEvent::all();

        foreach ($studentEvents as $event)
            DB::table('stored_events')->insert([
                'aggregate_uuid' => $event->aggregate_uuid,
                'event_class' => $event->event_class,
                'event_properties' => json_encode($event->event_properties),
                'meta_data' => json_encode($event->meta_data),
                'created_at' => $event->created_at,
            ]);

        //Drop student_stored_events table
        // Schema::dropIfExists('student_stored_events');
    }
}
