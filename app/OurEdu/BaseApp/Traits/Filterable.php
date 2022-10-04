<?php

namespace App\OurEdu\BaseApp\Traits;


use Illuminate\Database\Eloquent\Model;

trait Filterable
{
    public function applyFilters($model , array $filters) {
        foreach ($filters as $filter) {
            if(isset($filter['value'])){
                if (isset($filter['pipes'])) {
                    $filter = $this->applyPipes($filter);
                }
                $model = $this->{'apply' . ucfirst($filter['type']) . 'Filter'}($model , $filter);
            }
        }
        return $model;
    }

    public function applyInputFilter($model , $filter) {
        if(array_key_exists('trans' , $filter) && $filter['trans']) {
            return $model->whereTranslationLike($filter['name']  , '%' . $filter['value'] .'%');
        }
        return $model->where($filter['name']  , 'like' , '%' . $filter['value'] .'%');
    }

    public function applySelectFilter($model , $filter) {
        if(array_key_exists('trans' , $filter) && $filter['trans']) {
            return $model->whereTranslation($filter['name'] , $filter['value']);
        }
        return $model->where($filter['name']  ,  $filter['value']);
    }
    public function applySearchFilter($model , $filter) {

    }

    public function applyIdFilter($model , $filter) {

        return $model->where($filter['name'],  $filter['value']);
    }

    /*
     *  Relation Filter
     *  takes :-
     *      - name (relation name)
     *      - key (relation key name of relation eg : the primary key column in foreign table)
     *      - value (vale you are looking for)
     */

    public function applyRelationFilter($model , $filter) {

        return $model->whereHas($filter['relation'], function ($query) use ($filter){
                $query->where(  $filter['key'] ,  $filter['value']);
        });
    }

    public function applyPipes($filter){

        $pipes = explode("|", $filter['pipes']);
        foreach ($pipes as $pipe) {
            $filter['value'] = $this->{'apply' . $pipe . 'Pipe'}($filter['value']);
        }
        return $filter;
    }

    public function applyTrueFalsePipe($value){
        if ($value == 'true' || $value == 'yes'){
            return 1;
        } else {
            return 0;
        }
    }

}
