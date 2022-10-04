<?php

namespace App\OurEdu\Assessments\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentViewerType extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_type'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
