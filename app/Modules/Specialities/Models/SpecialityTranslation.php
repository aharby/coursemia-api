<?php


namespace App\Modules\Specialities\Models;


use Illuminate\Database\Eloquent\Model;

class SpecialityTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
    ];

}
