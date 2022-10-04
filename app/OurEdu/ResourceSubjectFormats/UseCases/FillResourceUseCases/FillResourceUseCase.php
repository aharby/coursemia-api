<?php


namespace App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases;

use App\OurEdu\Users\User;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PdfUseCase\FillPdfUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\AudioUseCase\FillAudioUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FlashUseCase\FillFlashUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\VideoUseCase\FillVideoUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PictureUseCase\FillPictureUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\PageUseCase\FillPageUseCaseUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\CompleteUseCase\FillCompleteUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MatchingUseCase\FillMatchingUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\TrueFalseUseCase\FillTrueFalseUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\DragDropUseCase\FillDragDropUseCaseUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultiMatchingUseCase\FillMultiMatchingUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\MultipleChoiceUseCase\FillMultipleChoiceUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\HotSpotUseCase\FillHotSpotUseCaseInterface;

class FillResourceUseCase implements FillResourceUseCaseInterface
{
    private $fillTrueFalseUseCase;

    private $fillVideoUseCase;
    private $fillDragDropUseCaseUseCase;

    private $fillMatchingUseCase;

    private $fillMultiMatchingUseCase;
    private $fillPageUseCaseUseCase;
    private $fillAudioUseCaseInterface;
    private $fillFlashUseCaseInterface;
    private $fillPdfUseCase;
    private $fillMultipleChoiceUseCase;
    private $fillPictureUseCase;
    private $fillCompleteUseCase;
    private $fillHotSpotUseCase;


    public function __construct(
        FillTrueFalseUseCaseInterface $fillTrueFalseUseCase,
        FillVideoUseCaseInterface $fillVideoUseCase,
        FillMatchingUseCaseInterface $fillMatchingUseCase,
        FillMultiMatchingUseCaseInterface $fillMultiMatchingUseCase,
        FillDragDropUseCaseUseCaseInterface $fillDragDropUseCaseUseCase,
        FillPageUseCaseUseCaseInterface $fillPageUseCaseUseCase,
        FillAudioUseCaseInterface $fillAudioUseCaseInterface,
        FillPdfUseCaseInterface $fillPdfUseCase,
        FillMultipleChoiceUseCaseInterface $fillMultipleChoiceUseCase,
        FillPictureUseCaseInterface $fillPictureUseCase,
        FillFlashUseCaseInterface $fillFlashUseCaseInterface,
        FillHotSpotUseCaseInterface $fillHotSpotUseCase,
        FillCompleteUseCaseInterface $fillCompleteUseCase
    ) {
        $this->fillTrueFalseUseCase = $fillTrueFalseUseCase;
        $this->fillVideoUseCase = $fillVideoUseCase;
        $this->fillMatchingUseCase = $fillMatchingUseCase;
        $this->fillMultiMatchingUseCase = $fillMultiMatchingUseCase;
        $this->fillDragDropUseCaseUseCase = $fillDragDropUseCaseUseCase;
        $this->fillPageUseCaseUseCase = $fillPageUseCaseUseCase;
        $this->fillAudioUseCaseInterface = $fillAudioUseCaseInterface;
        $this->fillFlashUseCaseInterface = $fillFlashUseCaseInterface;
        $this->fillPdfUseCase = $fillPdfUseCase;
        $this->fillMultipleChoiceUseCase = $fillMultipleChoiceUseCase;
        $this->fillPictureUseCase = $fillPictureUseCase;
        $this->fillCompleteUseCase = $fillCompleteUseCase;
        $this->fillHotSpotUseCase = $fillHotSpotUseCase;
    }


    public function fillResource(int $resourceSubjectFormatId, $data, User $user)
    {
        switch ($data->resource_slug) {
            case LearningResourcesEnums::TRUE_FALSE: //true_false
                return $this->fillTrueFalseUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::Video: //Video
                return $this->fillVideoUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::MATCHING: //matching
                return $this->fillMatchingUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::MULTIPLE_MATCHING: //multiple_matching
                return $this->fillMultiMatchingUseCase->fillResource($resourceSubjectFormatId, $data, $user);
            case LearningResourcesEnums::DRAG_DROP: //drag_drop
                return $this->fillDragDropUseCaseUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::PAGE: //page
                return $this->fillPageUseCaseUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::Audio: //audio
                return $this->fillAudioUseCaseInterface->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::FLASH: //flash
                return $this->fillFlashUseCaseInterface->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::PDF: //pdf
                return $this->fillPdfUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::MULTI_CHOICE: // MultipleChoice
                return $this->fillMultipleChoiceUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::COMPLETE: // Complete
                return $this->fillCompleteUseCase->fillResource($resourceSubjectFormatId, $data);
                break;
            case LearningResourcesEnums::PICTURE: //picture
                return $this->fillPictureUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
            case LearningResourcesEnums::HOTSPOT: //hot_spot
                return $this->fillHotSpotUseCase->fillResource($resourceSubjectFormatId, $data, $user);
                break;
        }
    }
}
