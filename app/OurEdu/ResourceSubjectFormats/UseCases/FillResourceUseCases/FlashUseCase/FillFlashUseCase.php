<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FlashUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashDataMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\Flash\FlashRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;

class FillFlashUseCase implements FillFlashUseCaseInterface
{
    private $flashRepository;
    private $flashData;

    public function __construct(FlashRepositoryInterface $flashRepository, FlashData $flashData)
    {
        $this->flashRepository = $flashRepository;
        $this->flashData = $flashData;
    }

    /**
     * @param int $resourceSubjectFormatId
     * @param $data
     * @param User $user
     * @return mixed|void
     */
    public function fillResource(int $resourceSubjectFormatId, $data, User $user)
    {
        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        $flashData = [
            'title' => $resourceSubjectFormatSubjectData->title,
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
        ];

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();
        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnFlashData = $this->flashRepository->getFlashDataBySubjectFormatId($resourceSubjectFormatId);
            if ($returnFlashData) {
                $flashObj = $this->flashRepository->update($returnFlashData, $flashData);
            } else {
                $flashObj = $this->flashRepository->create($flashData);
            }
            $oldIds = $flashObj->media()->pluck('id')->toArray();

            if ($resourceSubjectFormatSubjectData->attach_media) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attach_media->getId(), $flashObj->media(), 'subject/flashes');
                //To Remove Old & duplication
                deleteMedia($oldIds, $flashObj->media());
            }

            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->getId(), $flashObj->media());
            }


        } else {
            $update = $this->flashRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $oldIds = $update->media()->pluck('id')->toArray();

            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->getId(), $update->media());
            }
            if ($resourceSubjectFormatSubjectData->attach_media) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attach_media->getId(), $update->media(), 'subject/flashes');
                //To Remove Old & duplication
                deleteMedia($oldIds, $update->media());
            }
            $this->flashRepository->update($update, $flashData);
        }


    }
}
