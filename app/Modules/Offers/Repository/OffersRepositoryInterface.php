<?php

namespace App\Modules\Offers\Repository;

use App\Modules\Offers\Models\Offer;
use Illuminate\Support\Collection;

interface OffersRepositoryInterface
{
    public function all($isActive = false);

    public function find(int $id): Offer|null;

    public function create(array $attributes): Offer;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;

    public function pluck(): Collection;
}
