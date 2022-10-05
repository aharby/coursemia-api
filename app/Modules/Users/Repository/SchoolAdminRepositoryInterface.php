<?php


namespace App\Modules\Users\Repository;

use App\Modules\Users\Models\ContentAuthor;

interface SchoolAdminRepositoryInterface
{
    public function create(array $data);
    public function update(int $userId, array $data);
}
