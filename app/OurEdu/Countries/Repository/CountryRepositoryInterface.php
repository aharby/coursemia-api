<?php


namespace App\OurEdu\Countries\Repository;


use App\OurEdu\Countries\Country;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CountryRepositoryInterface
{
    public function all(): LengthAwarePaginator;
    public function find(int $id) : Country;
    public function create(array $attributes) : Country;
    public function update(int $id , array $attributes) : bool;
    public function delete(int $id) : bool;
    public function pluck() : Collection;
}
