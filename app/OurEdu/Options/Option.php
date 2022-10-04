<?php

namespace App\OurEdu\Options;

use App\OurEdu\BaseApp\BaseModel;
use Astrotomic\Translatable\Translatable;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Option extends BaseModel implements Auditable
{
    use SoftDeletes,
        CreatedBy,
        Translatable,
        HasFactory;
    use \OwenIt\Auditing\Auditable;

    ///////////////////////////// has translation
    protected $table = "options";

    protected $fillable = ['type' , 'is_active','slug','our_edu_reference'];

    protected $translatedAttributes = [
        'title'
    ];

    public $useTranslationFallback = true;


    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 1);
    }

    public function scopeNotDefault($query)
    {
        return $query->where('is_default', '=', 0);
    }

    /////////////////////// Options
    public function getOptionTypes()
    {
        return config('option_types');
    }

    public function getData()
    {
        return $this;
    }

    public function export($rows, $fileName)
    {
        if ($rows) {
            foreach ($rows as $row) {
                unset($object);
                $object['id']=$row->id;
                $object['Type']=$row->type;
                $object['Title']=$row->title;
                $object['Created at']=$row->created_at;
                $labels=array_keys($object);
                $data[]=$object;
            }
            export($data, $labels, $fileName);
        }
    }
}
