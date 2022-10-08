<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = new Category;
        $category->title_en = "Category title";
        $category->title_ar = "اسم الكاتيجوري";
        $category->save();
    }
}
