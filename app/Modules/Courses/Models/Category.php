<?php

namespace App\Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function getTitleAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["title_$lang"];
    }

    public function getHaveFreeContentAttribute(){
        $lectures = CourseLecture::where(['category_id' => $this->id, 'is_free_content' => 1])->first();
    }
}
