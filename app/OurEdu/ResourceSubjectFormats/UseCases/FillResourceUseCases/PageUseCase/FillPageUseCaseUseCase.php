<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PageUseCase;


use App\OurEdu\ResourceSubjectFormats\Repository\Page\PageRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Page\PageRepositoryInterface;

use App\OurEdu\Users\User;
use Illuminate\Support\Str;


class FillPageUseCaseUseCase implements FillPageUseCaseUseCaseInterface
{
    private $pageRepository;


    public function __construct(PageRepositoryInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user)
    {

        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;

        $pageData = [
            'title' => $resourceSubjectFormatSubjectData->title,
            'page' => $resourceSubjectFormatSubjectData->page,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();
        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnPage = $this->pageRepository->getPageDateBySubjectFormatId($resourceSubjectFormatId);
            if ($returnPage) {
                $page = $returnPage;
            } else {
                $page = $this->pageRepository->create($pageData);
            }

        } else {
            $page = $this->pageRepository->findOrFail($resourceSubjectFormatSubjectDataId);
        }
        $pageRepository = new PageRepository($page);

        $pageRepository->update([
            'page' => $resourceSubjectFormatSubjectData->page,
            'title' => $resourceSubjectFormatSubjectData->title,
        ]);



    }

}
