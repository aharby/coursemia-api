<?php


namespace App\Modules\Users\Repository;


use App\Modules\Users\Models\StudentTeacher;

class StudentTeacherRepository implements StudentTeacherRepositoryInterface
{
    public function create(array $data): ?StudentTeacher
    {
        return StudentTeacher::create($data);
    }

    public function findOrFail(int $id): ?StudentTeacher
    {
        return StudentTeacher::findOrFail($id);
    }

    public function update(StudentTeacher $studentTeacher, array $data): bool
    {
        return $studentTeacher->update($data);
    }

    public function delete(StudentTeacher $studentTeacher): bool
    {
        return $studentTeacher->delete();
    }
}
