<?php


namespace App\OurEdu\ResourceSubjectFormats\Repository\Page;


use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;


class PageRepository implements PageRepositoryInterface
{
    private $pageData;

    public function __construct(PageData $pageData)
    {
        $this->pageData = $pageData;
    }

    /**
     * @param int $id
     * @return PageData|null
     */
    public function findOrFail(int $id): ?PageData
    {
        return $this->pageData->findOrFail($id);
    }
    public function update(array $data)
    {
        return $this->pageData->update($data);
    }
    public function create(array $data): PageData
    {
        return $this->pageData->create($data);
    }
    public function getPageDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?PageData
    {
        return $this->pageData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }

}
