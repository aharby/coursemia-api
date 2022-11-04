<?php

namespace Database\Seeders;

use App\Modules\Offers\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class OffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $index = 0;
        $images = Storage::allFiles('uploads/large/offers/');
        while ($index < 100) {
            $offer = [
                'title:en' => "Title ${index}",
                'title:ar' => "عنوان ${index}",
                'is_active' => 1,
                'image' => str_replace('uploads/large/', '', $images[array_rand($images)]),
                'expiration_date' => Carbon::now()->addDays(rand(10,100)),
                'offer_value' => rand(10,100),
                'offer_type' => rand(1,2),
                'offer_code' => str_random(5)
            ];
            Offer::create($offer);
            $index++;
        }

    }
}
