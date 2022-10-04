<?php


namespace App\OurEdu\StaticPages\Repository;

use App\OurEdu\StaticBlocks\StaticBlock;
use App\OurEdu\StaticPages\StaticPage;

class StaticPagesRepository implements StaticPagesRepositoryInterface
{
    private $staticPage;

    public function __construct(StaticPage $staticPage)
    {
        $this->staticPage = $staticPage;
    }

    public function getPageBySlug($pageSlug, $blockSlug)
    {
        $staticPage = $this->staticPage->where('slug', $pageSlug)->firstOrFail();
        if (!is_null($blockSlug)) {
            return StaticBlock::where('page_id', $staticPage->id)->where('slug', $blockSlug)->firstOrFail();
        }
        return $staticPage;
    }

    public function paginate($perPage = 10)
    {
        return $this->staticPage->paginate($perPage);
    }

    public function create($data)
    {
        return $this->staticPage->create($data);
    }

    public function findOrFail($id)
    {
        return $this->staticPage->findOrFail($id);
    }

    public function update($id, $data)
    {
        return $this->staticPage->findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        return $this->staticPage->findOrFail($id)->delete();
    }
}
