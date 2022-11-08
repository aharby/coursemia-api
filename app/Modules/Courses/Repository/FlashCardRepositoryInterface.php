<?php


namespace App\Modules\Courses\Repository;

use App\Modules\Courses\Models\CourseFlashcard;
use Illuminate\Support\Collection;

interface FlashCardRepositoryInterface
{
    public function all($isActive = false);
    public function find(int $id) : CourseFlashcard|null;
    public function create(array $attributes) : CourseFlashcard;
    public function update(int $id, array $attributes) : bool;
    public function delete(int $id) : bool;
}
