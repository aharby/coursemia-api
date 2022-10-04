<?php

declare(strict_types=1);

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\VCRSchedules\Models\VCRRequest;

interface VCRRequestRepositoryInterface
{

    public function findOrFail(int $id): ?VCRRequest;

    public function create(array $data): VCRRequest;

    public function all();

    public function getInstructorRequests($instructorID);

    public function update(int $id, array $data): bool;

}
