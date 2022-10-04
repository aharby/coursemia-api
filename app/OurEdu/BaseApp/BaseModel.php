<?php

namespace App\OurEdu\BaseApp;

use App\OurEdu\Options\Option;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function getOptions($type=null)
    {
        $query= Option::active();
        if ($type) {
            $query=$query->where('type', $type);
        }
        return $query->listsTranslations('title')->pluck('title', 'id')->toArray();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
