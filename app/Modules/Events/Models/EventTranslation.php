<?php


namespace App\Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
    ];
}
