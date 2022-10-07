<?php


namespace App\Modules\Countries\Repository;


use App\Modules\Countries\Models\Country;
use Illuminate\Support\Collection;

interface CountryRepositoryInterface
{
    public function all($isActive=false);
    public function find(int $id) : Country;
    public function create(array $attributes) : Country;
    public function update(int $id , array $attributes) : bool;
    public function delete(int $id) : bool;
    public function pluck() : Collection;
}
