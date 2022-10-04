<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\SessionPreparationUseCase;

use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\SessionPreparation;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepositoryInterface;
use Swis\JsonApi\Client\Interfaces\DataInterface;

class SessionPreparationUseCase implements SessionPreparationUseCaseInterface
{


    /**
     * @var SessionPreparationRepositoryInterface
     */
    private $repository;

    public function __construct(SessionPreparationRepositoryInterface $preparationRepository)
    {
        $this->repository = $preparationRepository;
    }

    /**
     * @param DataInterface $data
     * @param int $sessionId
     * @param bool $isPublished
     * @return ClassroomClassSession
     */
    public function save(DataInterface $data, int $sessionId, bool $isPublished): ClassroomClassSession
    {
        // TODO::Use Repository when merge with dev
        $classSession = ClassroomClassSession::where(
            'instructor_id',
            auth()->id()
        )->with('preparation')->findOrFail($sessionId);

        if ($classSession->preparation) {
            $preparation = $classSession->preparation;
            $this->updatePreparationData($data, $preparation, $isPublished);
        } else {
            $preparation = $this->createPreparationData($data, $classSession, $isPublished);
        }
        if ($data->attachMedia) {
            $this->attachMedia($data, $preparation);
        }

        if ($data->detachMedia) {
            $this->detachMedia($data, $preparation);
        }

        $classSession = $classSession->load(['preparation.media']);
        if ($isPublished && $data->extraSessions && count($data->extraSessions)) {
            $this->clonePreparation($classSession, $data, $isPublished);
        }
        return $classSession;
    }

    /**
     * @param DataInterface $data
     * @param SessionPreparation $sessionPreparation
     * @param bool $isPublished
     */
    private function updatePreparationData(
        DataInterface $data,
        SessionPreparation $sessionPreparation,
        bool $isPublished
    ) {
        $preparationData = [
            'internal_preparation' => $data->internal_preparation,
            'pre_Learning' => $data->pre_Learning ?? null,
            'introductory' => $data->introductory ?? null,
            'application' => $data->application ?? null,
            'evaluation' => $data->evaluation ?? null,
            'section_id' => $data->section_id ?? null,
            'objectives' => $data->objectives ?? null,
        ];
        if (is_null($sessionPreparation->published_at) && $isPublished) {
            $preparationData['published_at'] = now();
        }
        $this->repository->update($sessionPreparation, $preparationData);
    }

    /**
     * @param DataInterface $data
     * @param ClassroomClassSession $classSession
     * @param bool $isPublished
     * @return SessionPreparation|null
     */
    private function createPreparationData(DataInterface $data, ClassroomClassSession $classSession, bool $isPublished)
    {
        $preparationData = [
            'subject_id' => $classSession->subject_id,
            'classroom_id' => $classSession->classroom_id,
            'classroom_class_id' => $classSession->classroom_class_id,
            'classroom_session_id' => $classSession->id,
            'internal_preparation' => $data->internal_preparation,
            'pre_Learning' => $data->pre_Learning ?? null,
            'introductory' => $data->introductory ?? null,
            'application' => $data->application ?? null,
            'evaluation' => $data->evaluation ?? null,
            'section_id' => $data->section_id ?? null,
            'objectives' => $data->objectives ?? null,
        ];
        if ($isPublished) {
            $preparationData['published_at'] = now();
        }
        return $this->repository->create($preparationData);
    }

    /**
     * @param DataInterface $data
     * @param               $preparation
     */
    private function attachMedia(DataInterface $data, $preparation)
    {
        foreach ($data->attachMedia as $media) {
            moveGarbageMediaWithColumns($media->getId(), $preparation->media(), 'preparations', [], [
                'name' => $media->name,
                'description' => $media->description,
                'subject_id' => $preparation->subject_id,
                'library' => $media->library ?? 0,
                'school_public' => 0
            ]);
        }
    }

    /**
     * @param DataInterface $data
     * @param SessionPreparation $preparation
     */
    private function detachMedia(DataInterface $data, SessionPreparation $preparation)
    {
        foreach ($data->detachMedia as $media) {
            deleteMedia($media->getId(), $preparation->media(), 'preparations');
        }
    }

    /**
     * @param DataInterface $data
     * @param ClassroomClassSession $classSession
     */
    private function clonePreparation(ClassroomClassSession $classSession, DataInterface $data, bool $isPublished)
    {
        $preparationData = [
            'subject_id' => $classSession->subject_id,
            'classroom_id' => $classSession->classroom_id,
            'classroom_class_id' => $classSession->classroom_class_id,
            'internal_preparation' => $data->internal_preparation,
            'pre_Learning' => $data->pre_Learning ?? null,
            'introductory' => $data->introductory ?? null,
            'application' => $data->application ?? null,
            'evaluation' => $data->evaluation ?? null,
            'section_id' => $data->section_id ?? null,
            'objectives' => $data->objectives ?? null,
            'published_at' => $isPublished ? now() : null,
        ];
        foreach ($data->extraSessions as $session) {
            $sessionPreparation = SessionPreparation::where('classroom_session_id', $session->id)->first();
            if (!isset($sessionPreparation) && $session->id != $classSession->id) {
                $preparationData['classroom_session_id'] = $session->id;
                $clonedPreparation = $this->repository->create($preparationData);
                if ($data->attachMedia) {
                    $this->cloneAttachedMedia($classSession, $clonedPreparation);
                }
            }
        }
    }

    private function cloneAttachedMedia(ClassroomClassSession $classSession, SessionPreparation $clonedPreparation)
    {
        $classSession->preparation->media()->each(function ($preparationtMediaToClone) use ($clonedPreparation) {
            $newFileName = 'preparations/' . time() . randString(10) . '.' . $preparationtMediaToClone->extension;
            $success = \Storage::copy(
                'public/uploads/large/' . $preparationtMediaToClone->filename,
                'public/uploads/large/' . $newFileName
            );
            if ($success) {
                $clonedPreparation->media()->create([
                    'name' => $preparationtMediaToClone->name,
                    'description' => $preparationtMediaToClone->description,
                    'subject_id' => $clonedPreparation->subject_id,
                    'library' => $preparationtMediaToClone->library ?? 0,
                    'source_filename' => $preparationtMediaToClone->source_filename,
                    'filename' => $newFileName,
                    'extension' => $preparationtMediaToClone->extension,
                    'mime_type' => $preparationtMediaToClone->mime_type,
                    'school_public' => 0
                ]);
            }
        });
    }
}
