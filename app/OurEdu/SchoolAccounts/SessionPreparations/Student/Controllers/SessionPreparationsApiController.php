<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\Student\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationStudent;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepositoryInterface;
use App\OurEdu\SchoolAccounts\SessionPreparations\Student\Transformers\ClassSessionsTransformer;
use App\OurEdu\SchoolAccounts\SessionPreparations\Student\Transformers\PreparationMediaTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return $this->transformDataMod($preparationMedia, new PreparationMediaTransformer(), ResourceTypesEnums::PREPARATION_MEDIA);
    }

    public function downloadMedia(PreparationMedia $preparationMedia)
    {
        $user = Auth::user();
        if($preparationMedia) {
            // $preparationMedia->student()->attach($user->id, ['downloaded_at' => now()]);
            $preparationMedia->student()
            ->syncWithoutDetaching([
                $user->id=>['downloaded_at' => now()]
            ]);
            return $this->successResponse(['success' => true],200);

        }

        return $this->errorResponse(['success' => false],500);

    }


    public function mediaLibrary(Request $request) {
        $user = Auth::user();
        $student = $user->student;

        $mediaLibrary = $this->preparationRepository->getStudentMediaLibrary($student->classroom, $request);

        return $this->transformDataMod($mediaLibrary, new PreparationMediaTransformer(), ResourceTypesEnums::PREPARATION_MEDIA);
    }
}
