<?php


namespace App\OurEdu\Users\Repository;

use App\OurEdu\Users\Models\ContentAuthor;

interface SchoolAdminRepositoryInterface
{
    public function create(array $data);
    public function update(int $userId, array $data);
}
