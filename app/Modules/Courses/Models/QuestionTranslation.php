<?php

namespace App\Modules\Courses\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'explanation',
    ];

}
