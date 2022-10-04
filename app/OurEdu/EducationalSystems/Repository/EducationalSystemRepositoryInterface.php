<?php


namespace App\OurEdu\EducationalSystems\Repository;



use App\OurEdu\EducationalSystems\EducationalSystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EducationalSystemRepositoryInterface
{
    public function all(array $filters) : LengthAwarePaginator;
    public function find(int $id) : EducationalSystem;
    public function create(array $attributes) : EducationalSystem;
    public function update(int $id , array $attributes) : bool;
    public function delete(int $id) : bool;
    public function pluck() : Collection;

    public function pluckByCountryId(int $countryId) : Collection;

}
