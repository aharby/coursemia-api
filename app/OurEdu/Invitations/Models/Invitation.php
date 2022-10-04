<?php

namespace App\OurEdu\Invitations\Models;

use App\OurEdu\Users\User;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_email',
        'receiver_id',
        'invitable_type',
        'invitable_id',
        'type',
        'status',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function invitable()
    {
        return $this->morphTo();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }
}
