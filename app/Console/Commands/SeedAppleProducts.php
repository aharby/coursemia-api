<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedAppleProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apple:seed-products
                            {currency? : The currency code}
                            {rate-exchange? : Currency rate exchange of dollar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed In-app Purchase Products from Apple';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $content = file_get_contents(
            database_path('seeders/data') . '/Apple_IAP_Products.json',
            true
        );
        $products = collect(json_decode($content, true));

        $currency = $this->argument('currency');
        $rate = $this->argument('rate-exchange') ?? 1;
        $data = $products->map(function ($value) use ($currency, $rate) {
            return [
                'product_id' => $value['productId'],
                'title' => $value['title'],
                'description' => $value['description'],
                'price' => $value['price'] * $rate,
                'currency' => $currency ?? $value['currency'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        DB::table('apple_iap_products')->truncate();
        DB::table('apple_iap_products')->insert($data);

        return 0;
    }
}
