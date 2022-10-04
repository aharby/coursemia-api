<?php

namespace App\OurEdu\StaticBlocks\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\StaticBlocks\StaticBlock;
use League\Fractal\TransformerAbstract;

class StaticBlockTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'blocks',
    ];

    public function transform(StaticBlock $staticBlock)
    {
        return [
            'id' => $staticBlock->id,
            'title' => $staticBlock->title,
            'slug' => $staticBlock->slug,
            'bg_image' => $staticBlock->bg_image,
            'icon' => $staticBlock->icon,
            'url' => $staticBlock->url,
            'body' => isset($staticBlock->body)?json_decode($staticBlock->body):[],
        ];
    }

    public function includeBlocks(StaticBlock $staticBlock)
    {
        $staticBlock = $staticBlock->childBlocks;

        if($staticBlock){
            return $this->Collection($staticBlock, new StaticBlockTransformer, ResourceTypesEnums::STATIC_BLOCK);
        }
    }
}
