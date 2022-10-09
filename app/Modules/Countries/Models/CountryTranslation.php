<?php


namespace App\Modules\Countries\Models;



use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
    ];

}
