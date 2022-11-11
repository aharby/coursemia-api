<?php

namespace App\Modules\Courses\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function ScopeFilter($query)
    {
        $query->when(request()->has('q'), function ($quer) {
            $quer->whereHas('user', function ($q) {
                $q->where('full_name', 'LIKE', '%'.request()->q.'%');
            });
        });
    }
}
