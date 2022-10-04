<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\EducationalSupervisor\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SessionPreparations\EducationalSupervisor\Transformers\ClassSessionsTransformer;
use App\OurEdu\SchoolAccounts\SessionPreparations\EducationalSupervisor\Transformers\PreparationMediaTransformer;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepositoryInterface;
use Illuminate\Http\Request;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class SessionPreparationsApiController extends BaseApiController
{
    /**
     * @var SessionPreparationRepositoryInterface
     */
    private $preparationRepository;
    /**
     * @var ParserInterface
     */
    private $Parser;

    /**
     * SessionPreparationsApiController constructor.
     * @param SessionPreparationRepositoryInterface $preparationRepository
     * @param ParserInterface $Parser
     */
    public function __construct(SessionPreparationRepositoryInterface $preparationRepository, ParserInterface $Parser)
    {
        $this->preparationRepository = $preparationRepository;
        $this->Parser = $Parser;
    }

    public function getSessions(Request $request, ClassroomClassSession $session)
    {
        $include = [
            'session_preparations.preparation_media','session_preparations.section',
        ];
        return $this->transformDataModIncludeItem($session, $include, new ClassSessionsTransformer(), ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }

    public function getSingleMedia(PreparationMedia $preparationMedia)
    {
        return $this->transformDataMod($preparationMedia,new PreparationMediaTransformer(),ResourceTypesEnums::PREPARATION_MEDIA);
    }
}
