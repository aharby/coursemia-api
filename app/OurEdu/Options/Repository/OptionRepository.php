<?php

namespace App\OurEdu\Options\Repository;

use App\OurEdu\Options\Option;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OptionRepository implements OptionRepositoryInterface
{

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return Option::orderBy('id', 'DESC')->paginate(env('PAGE_LIMIT', 20));
    }

    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return Option::orderBy('id', 'DESC')->paginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param int $id
     * @return Option|null
     */
    public function findOrFail(int $id): ?Option
    {
        return Option::findOrFail($id);
    }
    public function find(int $id): ?Option
    {
        return Option::find($id);
    }

    /**
     * @param array $data
     * @return Option
     */
    public function create(array $data): Option
    {
        return Option::create($data);
    }

    /**
     * @param Option $option
     * @param array $data
     * @return bool
     */
    public function update(Option $option, array $data): bool
    {
        return $option->update($data);
    }

    /**
     * @param Option $option
     * @return bool
     * @throws Exception
     */
    public function delete(Option $option): bool
    {
        return $option->delete();
    }

    /**
     * @return array
     */
    public function pluck(): array
    {
        return Option::with('translations')->where('is_active',1)->listsTranslations('title')->pluck('title', 'id')->toArray();
    }

    /**
     * @param string $type
     * @return array
     */
    public function pluckByType(string $type): array
    {
        return Option::where('type', $type)->with('translations')->listsTranslations('title')->pluck('title', 'id')->toArray();
    }

    public function pluckSlugByType(string $type): array
    {
        return Option::where('type', $type)->where('is_active',1)->pluck('slug', 'id')->toArray();
    }

    public function getOptionIdBySlug(string $slug): int
    {
        return Option::where('slug', $slug)->where('is_active',1)->first()->id;
    }
}
