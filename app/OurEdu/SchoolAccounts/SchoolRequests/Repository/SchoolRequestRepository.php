<?php

namespace App\OurEdu\SchoolAccounts\SchoolRequests\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Schools\School;
use App\OurEdu\Schools\SchoolRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SchoolRequestRepository
{
    use Filterable;

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return SchoolRequests::orderBy('id','DESC')->jsonPaginate(env('PAGE_LIMIT', 20));
    }


    /**
     * @param int $id
     * @return School|null
     */
    public function findOrFail(int $id): ?SchoolRequests
    {
        return SchoolRequests::findOrFail($id);
    }


    /**
     * @param array $data
     * @return School
     */
    public function create(array $data): SchoolRequests
    {
        return SchoolRequests::create($data);
    }


    /**
     * @param School $schoolRequest
     * @param array $data
     * @return bool
     */
    public function update(SchoolRequests $schoolRequest, array $data): bool
    {
        return $schoolRequest->update($data);
    }


    /**
     * @param School $schoolRequest
     * @return bool
     * @throws \Exception
     */
    public function delete(SchoolRequests $schoolRequest): bool
    {
        return $schoolRequest->delete();
    }

    public function pluck(): Collection
    {
        return SchoolRequest::with('translations')->listsTranslations('name')->pluck('name', 'id');
    }
}
