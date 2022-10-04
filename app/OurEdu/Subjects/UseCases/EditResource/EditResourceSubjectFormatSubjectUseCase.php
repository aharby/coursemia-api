<?php


namespace App\OurEdu\Subjects\UseCases\EditResource;

use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioDataMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashDataMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfDataMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureDataMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoDataMedia;
use App\OurEdu\ResourceSubjectFormats\Repository\Audio\AudioRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Flash\FlashRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Page\PageRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Page\PageRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Pdf\PdfRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Picture\PictureRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\Video\VideoRepositoryInterface;
use App\OurEdu\Subjects\UseCases\EditResource\CompleteUseCase\FillCompleteUseCaseInterface;
use App\OurEdu\Subjects\UseCases\EditResource\DragDropUseCase\FillDragDropUseCaseUseCaseInterface;
use App\OurEdu\Subjects\UseCases\EditResource\EditResourceSubjectFormatSubjectUseCaseInterface;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Subjects\UseCases\EditResource\HotSpotUseCase\FillHotSpotUseCaseInterface;
use App\OurEdu\Subjects\UseCases\EditResource\MatchingUseCase\FillMatchingUseCaseInterface;
use App\OurEdu\Subjects\UseCases\EditResource\MultiMatchingUseCase\FillMultiMatchingUseCaseInterface;
use App\OurEdu\Subjects\UseCases\EditResource\MultipleChoiceUseCase\FillMultipleChoiceUseCaseInterface;
use App\OurEdu\Subjects\UseCases\EditResource\TrueFalseUseCase\FillTrueFalseUseCaseInterface;
use Illuminate\Support\Str;


class EditResourceSubjectFormatSubjectUseCase implements EditResourceSubjectFormatSubjectUseCaseInterface
{
    private $pageRepository;
    private $videoRepository;
    private $audioRepository;
    private $pdfRepository;
    private $pictureRepository;
    private $flashRepository;
    private $fillCompleteUseCase;
    private $fillDragDropUseCaseUseCase;
    private $fillHotSpotUseCase;
    private $fillMatchingUseCase;
    private $fillMultiMatchingUseCase;
    private $fillMultipleChoiceUseCase;
    private $fillTrueFalseUseCase;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        VideoRepositoryInterface $videoRepository,
        AudioRepositoryInterface $audioRepository,
        PdfRepositoryInterface $pdfRepository,
        PictureRepositoryInterface $pictureRepository,
        FlashRepositoryInterface $flashRepository,
        FillCompleteUseCaseInterface $fillCompleteUseCase,
        FillDragDropUseCaseUseCaseInterface $fillDragDropUseCaseUseCase,
        FillHotSpotUseCaseInterface $fillHotSpotUseCase,
        FillMatchingUseCaseInterface $fillMatchingUseCase,
        FillMultiMatchingUseCaseInterface $fillMultiMatchingUseCase,
        FillMultipleChoiceUseCaseInterface $fillMultipleChoiceUseCase,
        FillTrueFalseUseCaseInterface $fillTrueFalseUseCase
    ) {
        $this->pageRepository = $pageRepository;
        $this->videoRepository = $videoRepository;
        $this->audioRepository = $audioRepository;
        $this->pdfRepository = $pdfRepository;
        $this->pictureRepository = $pictureRepository;
        $this->flashRepository = $flashRepository;
        $this->fillCompleteUseCase = $fillCompleteUseCase;
        $this->fillDragDropUseCaseUseCase = $fillDragDropUseCaseUseCase;
        $this->fillHotSpotUseCase = $fillHotSpotUseCase;
        $this->fillMatchingUseCase = $fillMatchingUseCase;
        $this->fillMultiMatchingUseCase = $fillMultiMatchingUseCase;
        $this->fillMultipleChoiceUseCase = $fillMultipleChoiceUseCase;
        $this->fillTrueFalseUseCase = $fillTrueFalseUseCase;
    }


    public function editResourceContent(int $resourceSubjectFormatId, $data)
    {
        switch ($data->resource_slug) {
            case LearningResourcesEnums::PAGE:
                return $this->editPageResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::Video:
                return $this->editVideoResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::Audio:
                return $this->editAudioResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::FLASH:
                return $this->editFlashResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::PDF:
                return $this->editPDFResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::PICTURE:
                return $this->editPictureResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::COMPLETE:
                return $this->fillCompleteUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::DRAG_DROP:
                return $this->fillDragDropUseCaseUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::HOTSPOT:
                return $this->fillHotSpotUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::TRUE_FALSE:
                return $this->fillTrueFalseUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::MATCHING:
                return $this->fillMatchingUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::MULTI_CHOICE:
                return $this->fillMultipleChoiceUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->fillMultiMatchingUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
        }
    }

    public function editPageResource($resourceSubjectFormatId, $data)
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

    public function editVideoResource($resourceSubjectFormatId, $data)
    {
        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        if (isset($resourceSubjectFormatSubjectData->video) && !empty($resourceSubjectFormatSubjectData->video)) {
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
            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->getId(), $returnVideoData->media());
            }
            if ($resourceSubjectFormatSubjectData->attachMedia) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attachMedia->getId(), $returnVideoData->media(), 'subject/videos');
                //To Remove Old & duplication
                deleteMedia($oldIds, $returnVideoData->media());
            }
        } else {
            $update = $this->videoRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $oldIds = $update->media()->pluck('id')->toArray();

            if ($resourceSubjectFormatSubjectData->attachMedia) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attachMedia->getId(), $update->media(), 'subject/videos');
                //To Remove Old & duplication
                deleteMedia($oldIds, $update->media());
            }
            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->getId(), new VideoDataMedia(), 'subject/videos');
            }
            $this->videoRepository->update($update, $videoData);
        }

    }

    public function editAudioResource($resourceSubjectFormatId, $data)
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

            if ($resourceSubjectFormatSubjectData->attachMedia) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attachMedia->getId(), $audioObj->media(), 'subject/audios');
                deleteMedia($oldIds, $audioObj->media());
            }

            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->getId(), $audioObj->media());
            }
        } else {
            $update = $this->audioRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $oldIds = $update->media()->pluck('id')->toArray();

            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->getId(), $update->media(), 'subject/audios');
            }
            if ($resourceSubjectFormatSubjectData->attachMedia) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attachMedia->getId(), $update->media(), 'subject/audios');
                deleteMedia($oldIds, $update->media());
            }
            $this->audioRepository->update($update, $audioData);
        }
    }

    public function editFlashResource($resourceSubjectFormatId, $data)
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

            if ($resourceSubjectFormatSubjectData->attachMedia) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attachMedia->getId(), $flashObj->media(), 'subject/flashes');
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
            if ($resourceSubjectFormatSubjectData->attachMedia) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attachMedia->getId(), $update->media(), 'subject/flashes');
                //To Remove Old & duplication
                deleteMedia($oldIds, $update->media());
            }
            $this->flashRepository->update($update, $flashData);
        }
    }

    public function editPDFResource($resourceSubjectFormatId, $data)
    {
        $resourceSubjectFormatSubjectData = $data->resource_subject_format_subject_data;
        if (isset($resourceSubjectFormatSubjectData->pdf) && !empty($resourceSubjectFormatSubjectData->pdf)) {
            $pdf_type = 'link';
            $link = $resourceSubjectFormatSubjectData->pdf;
            $pdfData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'pdf_type' => $pdf_type,
                'link' => $link
            ];
        } else {
            $pdf_type = 'pdf';
            $pdfData = [
                'title' => $resourceSubjectFormatSubjectData->title,
                'description' => $resourceSubjectFormatSubjectData->description,
                'resource_subject_format_subject_id' => $resourceSubjectFormatId,
                'pdf_type' => $pdf_type,
            ];
        }

        $resourceSubjectFormatSubjectDataId = $data->resource_subject_format_subject_data->getId();
        if (Str::contains($resourceSubjectFormatSubjectDataId, 'new')) {
            $returnPdfData = $this->pdfRepository->getPdfDateBySubjectFormatId($resourceSubjectFormatId);
            if ($returnPdfData) {
                $pdfObj = $this->pdfRepository->update($returnPdfData, $pdfData);
            } else {
                $pdfObj = $this->pdfRepository->create($pdfData);
            }
            $oldIds = $pdfObj->media()->pluck('id')->toArray();
            if ($data->attachMedia) {
                moveGarbageMedia($data->attachMedia->getId(), $pdfObj->media(), 'subject/pdfs');
                //To Remove Old & duplication
                deleteMedia($oldIds, $pdfObj->media());
            }
            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), new PdfDataMedia());
            }
        } else {
            $update = $this->pdfRepository->findOrFail($resourceSubjectFormatSubjectDataId);
            $oldIds = $update->media()->pluck('id')->toArray();

            if ($data->detach_media) {
                deleteMedia($data->detach_media->getId(), new PdfDataMedia());
            }
            if ($data->attachMedia) {
                moveGarbageMedia($resourceSubjectFormatSubjectData->attachMedia->getId(), $update->media(), 'subject/pdfs');
                //To Remove Old & duplication
                deleteMedia($oldIds, $update->media());
            }
            $this->pdfRepository->update($update, $pdfData);
        }
    }

    public function editPictureResource($resourceSubjectFormatId, $data)
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
            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->pluck('id')->toArray(),  $pictureObj->media());
            }
            if ($resourceSubjectFormatSubjectData->attachMedia) {
                $this->attachPictureMedia($resourceSubjectFormatSubjectData->attachMedia, $pictureObj->media());
            }
        } else {
            $update = $this->pictureRepository->findOrFail($resourceSubjectFormatSubjectDataId);

            if ($resourceSubjectFormatSubjectData->detach_media) {
                deleteMedia($resourceSubjectFormatSubjectData->detach_media->pluck('id')->toArray(), $update->media());
            }
            if ($resourceSubjectFormatSubjectData->attachMedia) {
                $this->attachPictureMedia($resourceSubjectFormatSubjectData->attachMedia, $update->media());
            }
            $this->updatePictureMediaDescription($resourceSubjectFormatSubjectData->picture_data_media);
            $this->pictureRepository->update($update, $pictureData);
        }
    }

    // for picture resource
    private function attachPictureMedia($attachMediaJsonRelation, $modelRelation)
    {
        if ($attachMediaJsonRelation) {
            $mediaIds = [];
            $extraColumns = [];
            if(is_object($attachMediaJsonRelation)){
                foreach ($attachMediaJsonRelation as $media) {
                    $mediaIds[] = $media->getId();
                    $extraColumns[$media->getId()] = [
                        'description' => $media->description
                    ];
                }
            }else{
                $mediaIds[] = $attachMediaJsonRelation->getId();

            }

            moveGarbageMedia($mediaIds, $modelRelation, 'subject/pictures', null, $extraColumns);
        }
    }

    // for picture resource
    private function updatePictureMediaDescription($pictureDataMediaJson)
    {
        if ($pictureDataMediaJson) {
            foreach ($pictureDataMediaJson as $media) {
                $pictureData = ['description' => $media->description];
                $this->pictureRepository->updateMedia($media->getId(), $pictureData);
            }
        }
    }
}
