<?php


namespace App\OurEdu\StaticBlocks\Repository;


use App\OurEdu\StaticBlocks\Repository\StaticBlocksRepositoryInterface;
use App\OurEdu\StaticBlocks\StaticBlock;

class StaticBlocksRepository implements StaticBlocksRepositoryInterface
{
    private $staticBlock;

    public function __construct(StaticBlock $staticBlock)
    {
        $this->staticBlock = $staticBlock;
    }

    public function get()
    {
        return $this->staticBlock->get();
    }

}
