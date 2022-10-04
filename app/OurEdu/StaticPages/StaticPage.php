<?php

namespace App\OurEdu\StaticPages;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\BaseApp\Traits\HasAttach;
use App\OurEdu\StaticBlocks\StaticBlock;
use Astrotomic\Translatable\Translatable;

class StaticPage extends BaseModel {
    use  Translatable;
    use  HasAttach;

    protected $fillable = [
        'slug',
        'is_active',
        'url',
        'bg_image',
    ];

    protected $translationForeignKey = 'static_page_id';

    protected $translatedAttributes = [
        'title',
        'body'
    ];

    protected static $attachFields = [
        'bg_image' => [
            'sizes' => ['small' => 'resize,256x144', 'large' => 'resize,1024x576'],
            'path' => 'uploads'
        ],
    ];

    public function staticBlocks()
    {
        return $this->hasMany(StaticBlock::class, 'page_id');
    }

}
