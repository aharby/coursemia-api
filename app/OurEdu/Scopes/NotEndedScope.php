<?php

namespace App\OurEdu\Scopes;

use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NotEndedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.Exa
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
//        if (\Auth::check()){
//            if (in_array(auth()->user()->type, [UserEnums::STUDENT_TYPE , UserEnums::PARENT_TYPE])) {
//                $builder->whereDate('end_date' , '>=' , now());
//            }
//        }
    }
}
