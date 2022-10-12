<?php

namespace App\Modules\Users\Admin\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens;
    protected $guarded = [];
    public function ScopeActive($query)
    {
        $query->where('is_active',1);
    }
}
