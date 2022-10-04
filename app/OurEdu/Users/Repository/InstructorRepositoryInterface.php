<?php


namespace App\OurEdu\Users\Repository;


use App\OurEdu\Users\Models\Instructor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InstructorRepositoryInterface
{
    public function all(): LengthAwarePaginator;
    public function create(array $data): ?Instructor;

    public function findOrFail(int $id): ?Instructor;

    public function update(Instructor $instructor, array $data): bool;

    public function delete(Instructor $instructor): bool;

    public function getInstructorByUserId(int $userId): ?Instructor;

    public function paginate(array $filters = [],$perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;

    public function pluck() : Collection;

    public function export() : Collection;


    public function getInstructorSessions($instructorId);
}
