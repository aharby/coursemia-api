<?php

namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\VideoUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoDataMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\Video\VideoRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;

class FillVideoUseCase implements FillVideoUseCaseInterface
{
    private $videoRepository;
    private $videoData;

    public function __construct(VideoRepositoryInterface $videoRepository, VideoData $videoData)
    {
        $this->videoRepository = $videoRepository;
        $this->videoData = $videoData;
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
        if ($resourceSubjectFormatSubjectData->video) {
            $video_type = 'url';
            $link = $resourceSubjectFormatSubjectData->video;
            $videoData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'video_type' => $video_type,
                'link' => $link
            ];
        } else {
            $video_type = 'video';

            $video = null;
            $videoData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'video_type' => $video_type,
            ];
        }

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();
        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnVideoData = $this->videoRepository->getVideoDateBySubjectFormatId($resourceSubjectFormatId);
            if ($returnVideoData) {
                $this->videoRepository->update($returnVideoData, $videoData);
            } else {

                $returnVideoData = $this->videoRepository->create($videoData);
            }

            $oldIds = $returnVideoData->media()->pluck('id')->toArray();
            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), $returnVideoData->media());
            }
            if ($data->attach_media) {
                moveGarbageMedia($data->attach_media->getId(), $returnVideoData->media(), 'subject/videos');
                //To Remove Old & duplication
                deleteMedia($oldIds, $returnVideoData->media());
            }
        } else {
            $update = $this->videoRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $oldIds = $update->media()->pluck('id')->toArray();

            if ($data->attach_media) {
                moveGarbageMedia($data->attach_media->getId(), $update->media(), 'subject/videos');
                //To Remove Old & duplication
                deleteMedia($oldIds, $update->media());
            }
            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), $update->media());
            }
            $this->videoRepository->update($update, $videoData);
        }
    }
}
