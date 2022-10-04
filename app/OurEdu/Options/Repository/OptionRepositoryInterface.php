<?php

namespace App\OurEdu\Options\Repository;

use App\OurEdu\Options\Option;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OptionRepositoryInterface
{

    public function all(): LengthAwarePaginator;


    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;


    /**
     * @param int $id
     * @return Option|null
     */
    public function findOrFail(int $id): ?Option;

    public function find(int $id): ?Option;

    /**
     * @param array $data
     * @return Option
     */
    public function create(array $data): Option;


    /**
     * @param Option $option
     * @param array $data
     * @return bool
     */
    public function update(Option $option, array $data): bool;


    /**
     * @param Option $option
     * @return bool
     */
    public function delete(Option $option): bool;


    /**
     * @return array
     */
    public function pluck(): array;

    /**
     * @param string $type
     * @return array
     */
    public function pluckByType(string $type): array;

    public function pluckSlugByType(string $type): array;

    public function getOptionIdBySlug(string $slug): int;
}
