<?php


namespace App\Modules\Users\Repository;


use App\Modules\Users\Models\ContentAuthor;
use App\Modules\Users\User;

class ContentAuthorRepository implements ContentAuthorRepositoryInterface
{

    public function create(array $data): ?ContentAuthor
    {
        return ContentAuthor::create($data);
    }

    public function findOrFail(int $id): ?ContentAuthor
    {
        return ContentAuthor::findOrFail($id);
    }

    public function update(ContentAuthor $contentAuthor, array $data): bool
    {
        return $contentAuthor->update($data);
    }

    public function delete(ContentAuthor $contentAuthor): bool
    {
        return $contentAuthor->delete();
    }

    public function getContentAuthorByUserId(int $userId): ?ContentAuthor
    {
        return ContentAuthor::where('user_id',$userId)->firstOrFail();
    }
}
