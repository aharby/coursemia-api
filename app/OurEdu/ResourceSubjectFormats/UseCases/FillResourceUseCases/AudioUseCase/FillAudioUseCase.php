<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\AudioUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioDataMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\Audio\AudioRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;

class FillAudioUseCase implements FillAudioUseCaseInterface
{
    private $audioRepository;
    private $audioData;

    public function __construct(AudioRepositoryInterface $audioRepository, AudioData $audioData)
    {
        $this->audioRepository = $audioRepository;
        $this->audioData = $audioData;
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
        if (isset($resourceSubjectFormatSubjectData->audio) && !empty($resourceSubjectFormatSubjectData->audio)) {
            $audio_type = 'url';
            $link = $resourceSubjectFormatSubjectData->audio;
            $audioData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'audio_type' => $audio_type,
                'link' => $link
            ];
        } else {
            $audio_type = 'audio';
            $audioData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'audio_type' => $audio_type,
            ];
        }
        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();
        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnAudioData = $this->audioRepository->getAudioDateBySubjectFormatId($resourceSubjectFormatId);

            if ($returnAudioData) {
                $audioObj = $this->audioRepository->update($returnAudioData, $audioData);
            } else {
                $audioObj = $this->audioRepository->create($audioData);
            }

            $oldIds = $audioObj->media()->pluck('id')->toArray();

            if ($data->attach_media) {
                moveGarbageMedia($data->attach_media->getId(), $audioObj->media(), 'subject/audios');
                deleteMedia($oldIds, $audioObj->media());
            }

            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), $audioObj->media());
            }
        } else {
            $update = $this->audioRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $oldIds = $update->media()->pluck('id')->toArray();

            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), $update->media(), 'subject/audios');
            }
            if ($data->attach_media) {
                moveGarbageMedia($data->attach_media->getId(), $update->media(), 'subject/audios');
                deleteMedia($oldIds, $update->media());
            }
            $this->audioRepository->update($update, $audioData);
        }
    }
}
