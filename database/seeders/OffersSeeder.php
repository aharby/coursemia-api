<?php

namespace Database\Seeders;

use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 1 ; $i++){
            $offer = new Offer();
            $offer->title_ar = "عنوان $i";
            $offer->title_en = "Title $i";
            $offer->image = "/uploads/events/event-1664893285.png";
            $offer->expiration_date = Carbon::now()->addMonth();
            $offer->offer_value = 20;
            $offer->offer_type = 1;
            $offer->offer_code = "ABCDEF";
            $offer->save();
        }
    }
}
