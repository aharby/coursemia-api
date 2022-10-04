<?php
declare(strict_types=1);

namespace App\OurEdu\StaticPages\Repository;

interface StaticPagesRepositoryInterface
{
    public function getPageBySlug($pageSlug, $blockSlug);
    public function paginate($perPage = 10);
    public function create($data);
    public function findOrFail($id);
    public function update($id , $data);
    public function delete($id);

}
