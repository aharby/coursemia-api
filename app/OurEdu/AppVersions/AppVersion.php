<?php

namespace App\OurEdu\AppVersions;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends BaseModel
{
    use SoftDeletes;

    protected $table = "app_versions";

    protected $fillable = [
        'type',
        'name',
        'version'
    ];
}
