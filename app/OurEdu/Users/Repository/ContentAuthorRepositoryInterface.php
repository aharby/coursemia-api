<?php


namespace App\OurEdu\Users\Repository;


use App\OurEdu\Users\Models\ContentAuthor;

interface ContentAuthorRepositoryInterface
{
    public function create(array $data): ?ContentAuthor;

    public function findOrFail(int $id): ?ContentAuthor;

    public function update(ContentAuthor $contentAuthor, array $data): bool;

    public function delete(ContentAuthor $contentAuthor) : bool;

    public function getContentAuthorByUserId (int $userId): ?ContentAuthor;
}