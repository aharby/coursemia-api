<?php

namespace App\OurEdu\Users\Models;

use App\OurEdu\Users\User;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentAuthor extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'content_authors';

    protected $fillable = ['hire_date','user_id'];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'content_author_task')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
