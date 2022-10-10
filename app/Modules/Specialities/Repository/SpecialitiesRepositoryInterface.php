<?php

namespace App\Modules\Specialities\Repository;

use App\Modules\Specialities\Models\Speciality;
use Illuminate\Support\Collection;

interface SpecialitiesRepositoryInterface
{
    public function all($isActive = false);

    public function find(int $id): Speciality|null;

    public function create(array $attributes): Speciality;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;

    public function pluck(): Collection;
}
