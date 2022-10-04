<?php

namespace App\OurEdu\LearningResources\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\LearningResources\Transformers\LearningResourceTransformer;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

use App\OurEdu\LearningResources\Repository\LearningResourceRepository;

use Swis\JsonApi\Client\Interfaces\ParserInterface;

class LearningResourceController extends BaseApiController
{
    private $module;
    private $repository;
    private $title;


    public function __construct(LearningResourceRepository $learningResourceRepository,
                                ParserInterface $parserInterface
    )
    {


        $this->module = 'learningResources';
        $this->repository = $learningResourceRepository;

        $this->title = trans('learning_resources.Learning Resources');
        $this->parserInterface = $parserInterface;

    }

    public function getIndex(BaseApiRequest $d)
    {

        $data = $this->repository->all();

        $include = '';
        return $this->transformDataModInclude($data, $include, new LearningResourceTransformer(), ResourceTypesEnums::LEARNING_RESOURCE);

    }

    public function getResource($slug)
    {
        $data = $this->repository->findResourceBy('slug',$slug);
        return $this->transformDataModInclude($data, '', new LearningResourceTransformer(), ResourceTypesEnums::LEARNING_RESOURCE);
    }


}
