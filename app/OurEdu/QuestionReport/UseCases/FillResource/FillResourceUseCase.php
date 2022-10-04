<?php


namespace App\OurEdu\QuestionReport\UseCases\FillResource;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\QuestionReport\UseCases\FillResource\Questions\CompleteUseCase\FillCompleteUseCase;
use App\OurEdu\QuestionReport\UseCases\FillResource\Questions\DragDropUseCase\FillDragDropUseCaseUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MatchingUseCase\FillMatchingUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultiMatchingUseCase\FillMultiMatchingUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultipleChoiceUseCase\FillMultipleChoiceUseCase;
use App\OurEdu\QuestionReport\UseCases\FillResource\Questions\MultipleChoiceUseCase\FillMultipleChoiceUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\FillResource\Questions\TrueFalseUseCase\FillTrueFalseUseCaseInterface;
use App\OurEdu\ResourceSubjectFormats\Repository\DragDrop\DragDropRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\Matching\MatchingRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\MultiMatching\MultiMatchingRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice\MultipleChoiceRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse\TrueFalseRepository;
use App\OurEdu\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FillResourceUseCase implements FillResourceUseCaseInterface
{
    private $fillTrueFalseUseCase;
    private $fillMatchingUseCase;
    private $fillMultiMatchingUseCase;
    private $fillDragDropUseCaseUseCase;
    private $fillMultipleChoiceUseCase;
    private $fillCompleteUseCase;

    public function __construct(
        FillTrueFalseUseCaseInterface $fillTrueFalseUseCase,
        FillMatchingUseCaseInterface $fillMatchingUseCase,
        FillMultiMatchingUseCaseInterface $fillMultiMatchingUseCase,
        FillDragDropUseCaseUseCaseInterface $fillDragDropUseCaseUseCase,
        FillMultipleChoiceUseCaseInterface $fillMultipleChoiceUseCase,
        FillCompleteUseCase $fillCompleteUseCase
    ) {
        $this->fillTrueFalseUseCase = $fillTrueFalseUseCase;
        $this->fillMatchingUseCase = $fillMatchingUseCase;
        $this->fillMultiMatchingUseCase = $fillMultiMatchingUseCase;
        $this->fillDragDropUseCaseUseCase = $fillDragDropUseCaseUseCase;
        $this->fillMultipleChoiceUseCase = $fillMultipleChoiceUseCase;
        $this->fillCompleteUseCase = $fillCompleteUseCase;
    }
    public function fillResource(int $questionId, $data)
    {
        switch ($data->slug) {
            case LearningResourcesEnums::TRUE_FALSE: //matching
                return $this->fillTrueFalseUseCase->fillResource($questionId, $data);
                break;
            case LearningResourcesEnums::MATCHING: //matching
                return $this->fillMatchingUseCase->fillResource($questionId, $data);
                break;
            case LearningResourcesEnums::MULTIPLE_MATCHING: //multiple_matching
                return $this->fillMultiMatchingUseCase->fillResource($questionId, $data);
            case LearningResourcesEnums::DRAG_DROP: //matching
                return $this->fillDragDropUseCaseUseCase->fillResource($questionId, $data);
                break;
            case LearningResourcesEnums::MULTI_CHOICE: // MultipleChoice
                return $this->fillMultipleChoiceUseCase->fillResource($questionId, $data);
                break;
            case LearningResourcesEnums::COMPLETE: // Complete
                return $this->fillCompleteUseCase->fillResource($questionId, $data);
                break;
        }
    }
}
