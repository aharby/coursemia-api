<?php


namespace App\Modules\Courses\Repository;

use App\Modules\Courses\Models\HostCourseRequest;

interface HostCourseRequestRepositoryInterface
{
    public function all($isActive = false);

    public function find(int $id): HostCourseRequest|null;

    public function create(array $attributes): HostCourseRequest;
}
