<?php

namespace App\Modules\CartItems\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Modules\Users\Models\User;
use App\Modules\Courses\Models\Course;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $fillable = ['user_id','course_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function emptyCart($userId){

        CartItem::where('user_id', $userId)->delete();

    }
}
