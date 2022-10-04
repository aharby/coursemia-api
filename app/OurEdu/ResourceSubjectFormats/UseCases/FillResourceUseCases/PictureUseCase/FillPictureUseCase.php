<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PictureUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureDataMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\Picture\PictureRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;

class FillPictureUseCase implements FillPictureUseCaseInterface
{
    private $pictureRepository;
    private $pictureData;

    public function __construct(PictureRepositoryInterface $pictureRepository, PictureData $pictureData)
    {
        $this->pictureRepository = $pictureRepository;
        $this->pictureData = $pictureData;
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
        $picture_type = 'picture';
        $pictureData = [
            'title' => $resourceSubjectFormatSubjectData->title,
            'description' => $resourceSubjectFormatSubjectData->description,
            'resource_subject_format_subject_id' => $resourceSubjectFormatId,
            'picture_type' => $picture_type,
        ];
        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();

        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnPictureData = $this->pictureRepository->getPictureDateBySubjectFormatId($resourceSubjectFormatId);
            if ($returnPictureData) {
                $pictureObj = $this->pictureRepository->update($returnPictureData, $pictureData);
            } else {
                $pictureObj = $this->pictureRepository->create($pictureData);
            }

            if ($data->detach_media) {
                deleteMedia($data->detach_media->pluck('id')->toArray(),  $pictureObj->media());
            }
            if ($data->attach_media) {
                $this->attachMedia($data->attach_media, $pictureObj->media());
            }
        } else {
            $update = $this->pictureRepository->findOrFail($resourceSubjectFormatSubjectDataId);

            if ($data->detach_media) {
                deleteMedia($data->detach_media->pluck('id')->toArray(), $update->media());
            }
            if ($data->attach_media) {
                $this->attachMedia($data->attach_media, $update->media());
            }
            $this->updateMediaDescription($data->picture_data_media);
            $this->pictureRepository->update($update, $pictureData);
        }
    }

    private function attachMedia($attachMediaJsonRelation, $modelRelation)
    {
        if ($attachMediaJsonRelation) {
            $extraColumns = [];
            foreach ($attachMediaJsonRelation as $media) {
                $extraColumns[$media->getId()] = [
                    'description' => $media->description
                ];
            }
            $mediaIds = $attachMediaJsonRelation->pluck('id')->toArray();
            moveGarbageMedia($mediaIds, $modelRelation, 'subject/pictures', null, $extraColumns);
        }
    }

    private function updateMediaDescription($pictureDataMediaJson)
    {
        if ($pictureDataMediaJson) {
            foreach ($pictureDataMediaJson as $media) {
                $pictureData = ['description' => $media->description];
                $this->pictureRepository->updateMedia($media->getId(), $pictureData);
            }
        }
    }
}
