<?php


namespace App\OurEdu\GradeColors\Models;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\HasAttach;
use App\OurEdu\GradeClasses\GradeClass;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeColor extends BaseModel
{
    use HasAttach;

    protected $fillable = [
        "slug",
        'image'
    ];

    /**
     * @return HasMany
     */
    public function gradeClasses()
    {
        return $this->hasMany(GradeClass::class);
    }
}
