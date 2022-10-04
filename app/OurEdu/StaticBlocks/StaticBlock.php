<?php

namespace App\OurEdu\StaticBlocks;

use App\OurEdu\BaseApp\BaseModel;
use Astrotomic\Translatable\Translatable;

class StaticBlock extends BaseModel {
    use  Translatable;

    protected $fillable = [
        'slug',
        'is_active',
        'url',
        'bg_image',
        'icon',
        'parent_id'
    ];

    protected $translationForeignKey = 'static_block_id';

    protected $translatedAttributes = [
        'title',
        'body'
    ];

    public function page()
    {
        return $this->belongsTo(StaticBlock::class, 'page_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(StaticBlock::class, 'parent_id', 'id');
    }

    public function childBlocks()
    {
        return $this->hasMany(StaticBlock::class, 'parent_id');
    }

}
