<?php

namespace App\OurEdu\Subjects\ContentAuthor\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

use App\OurEdu\Subjects\Enums\SectionTypesEnum;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\SME\Middleware\Api\SubjectPolicyMiddleware;
use App\OurEdu\Subjects\SME\Requests\AddSubjectMediaRequest;
use App\OurEdu\Subjects\SME\Requests\UpdateSubjectStructuralRequest;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Psy\Util\Str;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;

class SubjectApiController extends BaseApiController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countryRepository;
    private $educationalSystemRepository;
    private $updateSubjectStructuralUseCase;
    private $filters = [];

    public function __construct(SubjectRepository $subjectRepository,
                                ParserInterface $parserInterface,
                                UpdateSubjectStructuralUseCaseInterface $updateSubjectStructuralUseCase
    )
    {
        $this->middleware(SubjectPolicyMiddleware::class)->except(['getIndex']);


        $this->module = 'subjects';
        $this->repository = $subjectRepository;

        $this->title = trans('subjects.Subject');
        $this->parent = ParentEnum::SME;
        $this->parserInterface = $parserInterface;
        $this->updateSubjectStructuralUseCase = $updateSubjectStructuralUseCase;

    }

    public function getIndex(BaseApiRequest $d)
    {
        $userId = auth()->user()->id;

        $data = $this->repository->paginateWhereContentAuthor($userId);

        $include = '';
        return $this->transformDataModInclude($data, $include, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);

    }


}
