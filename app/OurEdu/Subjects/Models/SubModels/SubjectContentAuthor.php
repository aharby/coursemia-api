<?php

namespace App\OurEdu\Subjects\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;

class SubjectContentAuthor extends BaseModel
{
    protected $table = 'subject_content_author';
    protected $fillable = [
        'subject_id',
        'user_id',
        'created_at',
        'updated_at',

    ];

}
