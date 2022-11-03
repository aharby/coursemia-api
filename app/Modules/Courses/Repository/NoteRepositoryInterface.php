<?php


namespace App\Modules\Courses\Repository;

use App\Modules\Events\Models\Event;
use Illuminate\Support\Collection;

interface NoteRepositoryInterface
{
    public function all($isActive = false);
    public function find(int $id) : Event|null;
    public function create(array $attributes) : Event;
    public function update(int $id, array $attributes) : bool;
    public function delete(int $id) : bool;
    public function pluck() : Collection;
}
