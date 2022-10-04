<?php


namespace App\OurEdu\AcademicYears\Repository;


use App\OurEdu\AcademicYears\AcademicYear;
use Illuminate\Pagination\LengthAwarePaginator;

interface AcademicYearRepositoryInterface
{
    public function all(): LengthAwarePaginator;
    public function find(int $id) : AcademicYear;
    public function create(array $attributes) : AcademicYear;
    public function update(int $id , array $attributes) : bool;
    public function delete(int $id) : bool;
}
